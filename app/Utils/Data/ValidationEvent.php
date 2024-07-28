<?php
namespace Utils\Data;

use DateTime;
use Entity\User\Event;
use Exceptions\UserException;
use Models\User\Event as ModelUserEvent;

class ValidationEvent extends Validation
{
    /**
     * Check event
     * @param ?Event $event - event
     * @return bool
     * @throws UserException
     */
    public static function event(?Event $event): bool
    {
        if (empty($event)) throw new UserException(ModelUserEvent::EVENT_DOESNT_EXIST);
        if (!$event->isActive()) throw new UserException(ModelUserEvent::CODE_ALREADY_ACTIVATED);
        if ($event->getExpire() < new DateTime()) throw new UserException(ModelUserEvent::CODE_ALREADY_EXPIRED);
        if (empty($event->getUser()) || empty($event->getUser()->getEmail())) throw new UserException(ModelUserEvent::EVENT_EMAIL_EMPTY);
        return true;
    }

    /**
     * Check user event not exist
     * @param ?Event $event - event
     * @return bool
     * @throws UserException
     */
    public static function isEventNotExist(?Event $event): bool
    {
        if (!empty($event) && !empty($event->getCreated())) throw new UserException(ModelUserEvent::CODE_ALREADY_SENT);
        return true;
    }

    /**
     * Check user event is sent
     * @param ?Event $event - event
     * @return bool
     * @throws UserException
     */
    public static function isEventSend(?Event $event): bool
    {
        if (empty($event->getSend())) throw new UserException(ModelUserEvent::CODE_DIDNT_SEND);
        return true;
    }

    /**
     * Check user event is not sent
     * @param ?Event $event - event
     * @return bool
     * @throws UserException
     */
    public static function isEventNotSend(?Event $event): bool
    {
        if (!empty($event->getSend()) && $event->getSend() > new DateTime()) throw new UserException(ModelUserEvent::EVENT_ALREADY_SENT);
        return true;
    }
}
