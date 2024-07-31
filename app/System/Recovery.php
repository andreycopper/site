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
use Utils\Data\Handler;
use Utils\Data\ValidationEvent;
use Utils\Data\ValidationUser;

class Recovery {
    /**
     * Send recovery code
     * @param string $email - recovery email
     * @return string
     * @throws DbException|ForbiddenException|MailException|ReflectionException|UserException
     */
    public function submit(string $email): string
    {
        $email = Handler::toEmail($email);
        $searchEvent = Event::factory(['email' => $email, 'template' => ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY]);
        ValidationEvent::isEventNotExist($searchEvent);

        $user = User::factory(['email' => $email, 'active' => false]);
        ValidationUser::isValidActiveUser($user);

        $event = new Event(ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY, $email, $user);
        if (!$event->send()) throw new UserException(ModelUser::USER_NOT_SENT_CONFIRM);

        return $user->getEmail();
    }

    /**
     * Recovery password
     * @param Event $recoverEvent - recovery event
     * @return string
     * @throws DbException|ForbiddenException|MailException|ReflectionException|UserException
     */
    public function recover(Event $recoverEvent): string
    {
        ValidationUser::isValidActiveUser($recoverEvent->getUser());

        $date = new DateTime();
        $recoverEvent->getUser()->setPassword(password_hash(Request::post('password'), PASSWORD_DEFAULT))->setUpdated($date)->save();

        (new Event(ModelUserEvent::TEMPLATE_PASSWORD_CHANGED, $recoverEvent->getEmail(), $recoverEvent->getUser()))->send();

        $recoverEvent->setActive(false)->setUpdated($date)->save();
        return $recoverEvent->getUser()->getEmail();
    }
}
