<?php
namespace System;

use DateTime;
use Entity\User;
use Entity\User\Event;
use Exceptions\DbException;
use Exceptions\ForbiddenException;
use ReflectionException;
use Exceptions\MailException;
use Models\User as ModelUser;
use Exceptions\UserException;
use Models\User\Event as ModelUserEvent;
use Utils\Data\ValidationUser;

class Recovery {
    private ?string $email;
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
     * @return void
     * @throws MailException|ReflectionException|UserException|DbException|ForbiddenException
     */
    public function submit(): void
    {
        $this->user = User::factory(['email' => $this->email]);
        ValidationUser::isValidActiveUser($this->user);

        $this->event = new Event(ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY, $this->user, ['user_email' => $this->email]);
        if (!$this->event->send()) throw new UserException(ModelUser::USER_NOT_SENT_CONFIRM);

        if (Request::isAjax()) Response::result(200, true, $this->user->getEmail());
        else {
            header("Location: /recover/success/{$this->user->getEmail()}");
            die;
        }
    }

    /**
     * Recovery password
     * @return void
     * @throws MailException|ReflectionException|UserException
     */
    public function recover(): void
    {
        $date = new DateTime();
        $this->user->setPassword(password_hash(Request::post('password'), PASSWORD_DEFAULT))->setUpdated($date)->save();

        $recoverEvent = new Event(ModelUserEvent::TEMPLATE_PASSWORD_CHANGED, $this->user, ['user_email' => $this->email]);
        $recoverEvent->send();

        $this->event->setActive(false)->setUpdated($date)->save();

        if (Request::isAjax()) Response::result(200, true, $this->user->getEmail());
        else {
            header("Location: /recover/finish/{$this->user->getEmail()}/");
            die;
        }
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
