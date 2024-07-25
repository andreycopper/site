<?php
namespace Utils\Data;

use Entity\User;
use Entity\Bookmark;
use Exceptions\UserException;
use Exceptions\ForbiddenException;

class ValidationBookmark extends Validation
{
    const BOOKMARK_EMPTY = 'Bookmark is not found';
    const BOOKMARK_EXIST = 'Bookmark is already exist';
    const NOT_ALLOWED_REMOVE = 'You aren\'t allowed to remove the bookmark';

    /**
     * Check bookmark
     * @param ?Bookmark $bookmark - bookmark
     * @return bool
     * @throws UserException
     */
    public static function isValidBookmark(?Bookmark $bookmark): bool
    {
        if (empty($bookmark) || empty($bookmark->getUserId()) || empty($bookmark->getMark()) || empty($bookmark->getMark()->getId()))
            throw new UserException(self::BOOKMARK_EMPTY);
        return true;
    }

    /**
     * Check empty bookmark
     * @param ?Bookmark $bookmark - bookmark
     * @return bool
     * @throws UserException
     */
    public static function isEmptyBookmark(?Bookmark $bookmark): bool
    {
        if (!empty($bookmark) && !empty($bookmark->getId())) throw new UserException(self::BOOKMARK_EXIST);
        return true;
    }

    /**
     * Check access to change bookmark
     * @param ?Bookmark $bookmark - bookmark
     * @param ?User $user - user
     * @param ?string $action - action
     * @return bool
     * @throws ForbiddenException|UserException
     */
    public static function canChangeBookmark(?Bookmark $bookmark, ?User $user, ?string $action): bool
    {
        self::isValidBookmark($bookmark);

        if (empty($user)) throw new ForbiddenException(self::ACCESS_DENIED);

        if ($user->getId() !== $bookmark->getUserId() && $user->getId() !== $bookmark->getMark()->getId())
            throw new ForbiddenException(self::ACCESS_DENIED);

        switch ($action) {
            case 'remove':
                if ($user->getId() !== $bookmark->getUserId()) throw new ForbiddenException(self::NOT_ALLOWED_REMOVE);
                break;
            default:
                throw new ForbiddenException(self::NOT_ALLOWED_ACTION);
        }

        return true;
    }
}
