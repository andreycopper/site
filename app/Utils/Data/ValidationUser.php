<?php
namespace Utils\Data;

use Entity\User;
use System\Auth;
use Entity;
use Entity\IpBlock;
use Entity\User\Block;
use ReflectionException;
use Exceptions\DbException;
use Models\User as ModelUser;
use Exceptions\UserException;
use Exceptions\ForbiddenException;
use Models\User\Session as ModelUserSession;

class ValidationUser extends Validation
{
    /**
     * Check user
     * @param ?User $user
     * @return bool
     * @throws UserException|DbException|ForbiddenException
     */
    public static function isValidUser(?User $user): bool
    {
        if (empty($user) || empty($user->getId())) throw new UserException(self::USER_NOT_FOUND, 400);
        return self::isNoUserBlocks($user) && self::isUserAuthAttemptsNotExceeded($user);
    }

    /**
     * Check user
     * @param ?User $user
     * @return bool
     * @throws UserException|DbException|ForbiddenException
     */
    public static function isValidActiveUser(?User $user): bool
    {
        self::isValidUser($user);
        if (!$user->isActive()) throw new UserException(self::USER_NOT_ACTIVE, 400);
        return true;
    }

    /**
     * Check user
     * @param ?User $user
     * @return bool
     * @throws UserException|DbException|ForbiddenException
     */
    public static function isValidNotActiveUser(?User $user): bool
    {
        self::isValidUser($user);
        if ($user->isActive()) throw new UserException(self::USER_ACTIVE, 400);
        return true;
    }

    /**
     * Checking blocked users
     * @param User $user - user
     * @return bool
     * @throws ForbiddenException
     */
    private static function isNoUserBlocks(User $user): bool
    {
        if ($user->isBlocked()) throw new ForbiddenException(self::USER_IS_BLOCKED, 403);

        if (!empty($user->getBlock()) && !empty($user->getBlock()->getExpire())) {
            $dt = "{$user->getBlock()->getExpire()->format('d.m.Y H:i')} {$user->getBlock()->getExpire()->getTimezone()->getName()}";
            throw new ForbiddenException(self::USER_TEMPORARILY_BLOCKED_TILL . $dt . '<br>' . self::REASON . ": {$user->getBlock()->getReason()}", 403);
        }

        return true;
    }

    /**
     * Checking user's failed auth attempts
     * @param User $user - user
     * @return bool
     * @throws DbException
     * @throws ForbiddenException
     */
    private static function isUserAuthAttemptsNotExceeded(User $user): bool
    {
        $failedAttempts = ModelUserSession::getFailedAttempts($user->getEmail(), $_SERVER['REMOTE_ADDR']);

        if ($failedAttempts['by_ip'] >= Auth::MAX_FAILED_ATTEMPTS_BY_IP) {
            $ipBlock = new IpBlock($_SERVER['REMOTE_ADDR'], $user->getId(), $user->getEmail(), self::TOO_MANY_FAILED_ATTEMPTS_BY_IP . " {$_SERVER['REMOTE_ADDR']}");
            $ipBlock->save();
            ModelUserSession::clearFailedAttempts($user->getEmail());

            $dt = "{$ipBlock->getExpire()->format('d.m.Y H:i')} {$ipBlock->getExpire()->getTimezone()->getName()}";
            throw new ForbiddenException(self::USER_IP_TEMPORARILY_BLOCKED_TILL . $dt . '<br>' . self::REASON . ": {$ipBlock->getReason()}", 403);
        }

        if ($failedAttempts['total'] >= Auth::MAX_TOTAL_FAILED_ATTEMPTS) {
            $userBlock = new Block($user->getId(), self::TOO_MANY_FAILED_ATTEMPTS_BY_LOGIN . " {$user->getEmail()}");
            $userBlock->save();
            ModelUserSession::clearFailedAttempts($user->getEmail());

            $dt = "{$userBlock->getExpire()->format('d.m.Y H:i')} {$userBlock->getExpire()->getTimezone()->getName()}";
            throw new ForbiddenException(self::USER_TEMPORARILY_BLOCKED_TILL . $dt . '<br>' . self::REASON . ": {$userBlock->getReason()}", 403);
        }

        return true;
    }

    /**
     * Check existing user email
     * @param $email - email
     * @return bool
     * @throws UserException|ReflectionException
     */
    public static function isExistUserEmail($email): bool
    {
        if (empty(User::factory(['email' => $email]))) throw new UserException(ModelUser::EMAIL_EXIST);
        return true;
    }

    /**
     * Check not existing user email
     * @param $email - email
     * @return bool
     * @throws UserException|ReflectionException
     */
    public static function isNotExistUserEmail($email): bool
    {
        if (!empty(User::factory(['email' => $email, 'active' => false]))) throw new UserException(ModelUser::EMAIL_EXIST);
        return true;
    }

    /**
     * Checking existing activated user login
     * @param ?string $email - user email
     * @return bool
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    public static function isExistActiveUserEmail(?string $email): bool
    {
        self::isValidEmail($email);
        $user = User::factory(['email' => $email, 'active' => false]);
        self::isValidUser($user);
        if (!$user->isActive()) throw new UserException(ModelUser::USER_NOT_ACTIVE);
        return true;
    }

    /**
     * Checking existing not activated user login
     * @param ?string $email - user email
     * @return bool
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    public static function isExistNotActiveUserEmail(?string $email): bool
    {
        self::isValidEmail($email);
        $user = User::factory(['email' => $email, 'active' => false]);
        self::isValidUser($user);
        if ($user->isActive()) throw new UserException(ModelUser::ALREADY_ACTIVATED);
        return true;
    }
}
