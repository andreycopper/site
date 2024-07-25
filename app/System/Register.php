<?php
namespace System;

use DateTime;
use Entity\User;
use Utils\Data;
use Utils\Data\ValidationUser;
use Entity\User\Event;
use ReflectionException;
use Exceptions\MailException;
use Models\User as ModelUser;
use Exceptions\UserException;
use Models\User\Event as ModelUserEvent;

class Register {
    private string $login;
    private string $password;
    private string $email;

    /**
     * @param string $login - login
     * @param string $password - password
     * @param string $email - email
     */
    public function __construct(string $login, string $password, string $email)
    {
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Register
     * @throws UserException|MailException|ReflectionException
     */
    public function register(?Event $eventInvitation = null): void
    {
        ValidationUser::isNotExistUserLogin($this->login);
        ValidationUser::isNotExistUserEmail($this->email);

        if (!(new User($this->login, $this->password, $this->email))->save()) throw new UserException(ModelUser::USER_NOT_SAVED);
        $user = User::factory(['login' => $this->login, 'active' => false]);

        if (!(new Crypt())->generatePair()->save($user->getId())) throw new UserException(ModelUser::USER_NOT_CRYPT_KEY);

        $eventRegister = new Event(ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, $user->getId());
        if (!$eventRegister->send()) throw new UserException(ModelUser::USER_NOT_SENT_CONFIRM);

        if (!empty($eventInvitation)) $eventInvitation->setActive(false)->save();

        if (Request::isAjax()) Response::result(200, true, $user->getLogin());
        else {
            header("Location: /register/success/{$user->getLogin()}");
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
        $regEvent = new Event(ModelUserEvent::TEMPLATE_REGISTER, $event->getUser()->getId());
        $regEvent->send();

        header("Location: /register/finish/{$event->getUser()->getLogin()}/");
        die;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): Register
    {
        $this->login = $login;
        return $this;
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
