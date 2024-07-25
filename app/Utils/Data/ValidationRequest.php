<?php
namespace Utils\Data;

use Entity\User;
use Entity\Request;
use Exceptions\UserException;
use Exceptions\ForbiddenException;

class ValidationRequest extends Validation
{
    const REQUEST_EMPTY = 'Request is not found';
    const REQUEST_SENT = 'Request already sent';
    const REQUEST_RECEIVED = 'Request already received';
    const REQUEST_DECLINED = 'Request already declined';
    const REQUEST_USER_DECLINED = 'User already declined your request';
    const REQUEST_ACCEPTED = 'Request already accepted';
    const REQUEST_USER_ACCEPTED = 'User already accepted your request';
    const NOT_ALLOWED_ACCEPT = 'You aren\'t allowed to accept the request';
    const NOT_ALLOWED_DECLINE = 'You aren\'t allowed to decline the request';
    const NOT_ALLOWED_CANCEL = 'You aren\'t allowed to cancel the request';
    const NOT_FRIEND = 'User is not in friends list';
    const UNKNOWN_REQUEST_TYPE = 'Unknown request type';

    /**
     * Check message request and access
     * @param ?Request $request
     * @return bool
     * @throws UserException
     */
    public static function isValidRequest(?Request $request): bool
    {
        if (empty($request) || empty($request->getId()) || empty($request->getCreated()) || empty($request->getUserId()) ||
            empty($request->getToId())) throw new UserException(self::REQUEST_EMPTY);
        return true;
    }

    /**
     * Check empty request
     * @param ?Request $request
     * @return bool
     * @throws UserException
     */
    public static function isEmptyRequest(?Request $request): bool
    {
        if (!empty($request) && !empty($request->getId()) && !empty($request->getCreated())) {
            if (!empty($request->getDeclined())) throw new UserException(self::REQUEST_USER_DECLINED);
            elseif (!empty($request->getAccepted())) throw new UserException(self::REQUEST_USER_ACCEPTED);
            else throw new UserException(self::REQUEST_SENT);
        }
        return true;
    }

    public static function canMakeRequest(?Request $request1, ?Request $request2): bool
    {
        if (!empty($request1)) {
            if (!empty($request1->getDeclined())) throw new UserException(self::REQUEST_USER_DECLINED);
            elseif (!empty($request1->getAccepted())) throw new UserException(self::REQUEST_USER_ACCEPTED);
            else throw new UserException(self::REQUEST_SENT);
        }

        if (!empty($request2)) {
            if (!empty($request2->getDeclined())) throw new UserException(self::REQUEST_DECLINED);
            elseif (!empty($request2->getAccepted())) throw new UserException(self::REQUEST_ACCEPTED);
            else throw new UserException(self::REQUEST_RECEIVED);
        }

        return true;
    }

    /**
     * Check user is friend
     * @param ?Request $request1
     * @param ?Request $request2
     * @return bool
     * @throws UserException
     */
    public static function isFriend(?Request $request1, ?Request $request2): bool
    {
        if ((empty($request1) || empty($request1->getAccepted())) &&
            (empty($request2) || empty($request2->getAccepted()))) throw new UserException(self::NOT_FRIEND);
        return true;
    }

    /**
     * @param ?Request $request - message request
     * @param ?User $user - user
     * @param ?string $action - action
     * @return bool
     * @throws ForbiddenException|UserException
     */
    public static function canChangeRequest(?Request $request, ?User $user, ?string $action): bool
    {
        self::isValidRequest($request);
        if (empty($user)) throw new ForbiddenException(self::ACCESS_DENIED);
        if ($user->getId() !== $request->getUserId() && $user->getId() !== $request->getToId())
            throw new ForbiddenException(self::ACCESS_DENIED);

        switch ($action) {
            case 'accept':
                if ($user->getId() !== $request->getToId()) throw new ForbiddenException(self::NOT_ALLOWED_ACCEPT);
                elseif (!empty($request->getAccepted())) throw new UserException(self::REQUEST_ACCEPTED);
                break;
            case 'decline':
                if ($user->getId() !== $request->getToId()) throw new ForbiddenException(self::NOT_ALLOWED_DECLINE);
                elseif (!empty($request->getDeclined())) throw new UserException(self::REQUEST_DECLINED);
                break;
            case 'cancel':
                if ($user->getId() !== $request->getUserId()) throw new ForbiddenException(self::NOT_ALLOWED_CANCEL);
                elseif (!empty($request->getDeclined())) throw new UserException(self::REQUEST_USER_DECLINED);
                elseif (!empty($request->getAccepted())) throw new UserException(self::REQUEST_USER_ACCEPTED);
                break;
            default:
                throw new ForbiddenException(self::NOT_ALLOWED_ACTION);
        }

        return true;
    }
}
