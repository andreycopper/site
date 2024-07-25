<?php
namespace System;

use DateTime;
use Entity\User;
use Entity\User\Event;
use ReflectionException;
use Exceptions\MailException;
use Models\User as ModelUser;
use Exceptions\UserException;
use Models\User\Event as ModelUserEvent;

class Recovery {
    private string $email;
    private ?User $user;
    private ?Event $event;

    /**
     * @param ?string $email - email
     */
    public function __construct(?string $email = null)
    {
        $this->email = $email;
    }

    /**
     * Send recovery code
     * @throws UserException|MailException|ReflectionException
     */
    public function submit(): void
    {
        $this->user = User::factory(['email' => $this->email]);
        if (empty($this->user)) throw new UserException(ModelUser::USER_NOT_FOUND);

        $this->event = new Event(ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY, $this->user->getId());
        if (!$this->event->save())
            throw new UserException(ModelUser::USER_NOT_SENT_CONFIRM);

        $this->event->send();
        header("Location: /recover/success/{$this->user->getLogin()}");
        die;
    }

    /**
     * Recovery password
     * @return void
     * @throws MailException|ReflectionException
     */
    public function recover(): void
    {
        $date = new DateTime();
        $this->user->setPassword(password_hash(Request::post('password'), PASSWORD_DEFAULT))->setUpdated($date)->save();
        $this->event->setActive(false)->setUpdated($date)->save();


        $regEvent = new Event(ModelUserEvent::TEMPLATE_PASSWORD_CHANGED, $this->user->getId());
        $regEvent->save();
        $regEvent->send();

        header("Location: /recover/finish/{$this->user->getLogin()}/");
        die;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Recovery
    {
        $this->email = $email;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Recovery
    {
        $this->user = $user;
        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): Recovery
    {
        $this->event = $event;
        return $this;
    }
}
