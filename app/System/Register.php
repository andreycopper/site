<?php
namespace System;

use DateTime;
use Entity\User;
use Entity\User\Event;
use ReflectionException;
use Exceptions\MailException;
use Models\User as ModelUser;
use Exceptions\UserException;
use Utils\Data\ValidationUser;
use Models\User\Event as ModelUserEvent;

class Register {
    private string $email;
    private string $password;

    /**
     * @param string $email - email
     * @param string $password - password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Register
     * @throws UserException|MailException|ReflectionException
     */
    public function register(): void
    {
        ValidationUser::isNotExistUserEmail($this->email);

        if (!(new User($this->email, $this->password))->save()) throw new UserException(ModelUser::USER_NOT_SAVED);
        $user = User::factory(['email' => $this->email, 'active' => false]);

        if (!(new Crypt())->generatePair()->save($user->getId())) throw new UserException(ModelUser::USER_NOT_CRYPT_KEY);

        $eventRegister = new Event(ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, $user, ['user_email' => $this->email]);
        if (!$eventRegister->send()) throw new UserException(ModelUser::USER_NOT_SENT_CONFIRM);

        if (Request::isAjax()) Response::result(200, true, $user->getEmail());
        else {
            header("Location: /register/success/{$user->getEmail()}");
            die;
        }
    }

    /**
     * @param Event $event - confirm event
     * @return void
     * @throws MailException|ReflectionException|UserException
     */
    public function confirm(Event $event): void
    {
        $date = new DateTime();
        $event->getUser()->setActive(true)->setUpdated($date)->save();
        $event->setActive(false)->setUpdated($date)->save();
        $regEvent = new Event(ModelUserEvent::TEMPLATE_REGISTER, $event->getUser());
        $regEvent->send();

        if (Request::isAjax()) Response::result(200, true, $event->getUser()->getEmail());
        else {
            header("Location: /register/finish/{$event->getUser()->getEmail()}/");
            die;
        }
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): Register
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Register
    {
        $this->email = $email;
        return $this;
    }
}
