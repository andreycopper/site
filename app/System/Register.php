<?php
namespace System;

use DateTime;
use Entity\User;
use Entity\User\Event;
use Utils\Data\Handler;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\MailException;
use Models\User as ModelUser;
use Exceptions\UserException;
use Utils\Data\ValidationUser;
use Utils\Data\ValidationEvent;
use Exceptions\ForbiddenException;
use Models\User\Event as ModelUserEvent;

class Register {
    private ?Event $event;

    /**
     * @param ?int $eventType - event type
     * @param ?string $code - event code
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    public function __construct(?int $eventType = null, ?string $code = null)
    {
        if (!empty($eventType) && !empty($code)) {
            ValidationEvent::isValidCode($code);
            $this->event = Event::factory(['code' => $code, 'template' => $eventType, 'active' => false]);
            ValidationEvent::event($this->event);

            if (!empty($this->event->getUser())) ValidationUser::isValidUser($this->event->getUser());
        }
    }

    /**
     * Register
     * @param string $email - email
     * @param string $password - password
     * @return string
     * @throws DbException|ForbiddenException|MailException|ReflectionException|UserException
     */
    public function register(string $email, string $password): string
    {
        $email = Handler::toEmail($email);
        $password = Handler::toPassword($password);
        ValidationUser::isNotExistUserEmail($email);

        if (!(new User($email, $password))->save()) throw new UserException(ModelUser::USER_NOT_SAVED);
        $user = User::factory(['email' => $email, 'active' => false]);
        ValidationUser::isValidUser($user);
        if (!(new Crypt())->generatePair()->save($user->getId())) throw new UserException(ModelUser::USER_NOT_CRYPT_KEY);

        $regEvent = new Event(ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, $user->getEmail(), $user);
        if (!$regEvent->send()) throw new UserException(ModelUser::USER_NOT_SENT_CONFIRM);

        return $user->getEmail();
    }

    /**
     * Register confirm
     * @return string
     * @throws MailException|ReflectionException|UserException
     */
    public function confirm(): string
    {
        $date = new DateTime();
        $this->event->getUser()->setActive(true)->setUpdated($date)->save();
        $this->event->setActive(false)->setUpdated($date)->save();

        $regEvent = new Event(ModelUserEvent::TEMPLATE_REGISTER, $this->event->getUser()->getEmail(), $this->event->getUser());
        if (!$regEvent->send()) throw new UserException(ModelUser::USER_NOT_SENT_VERIFY);

        return $this->event->getUser()->getEmail();
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): void
    {
        $this->event = $event;
    }
}
