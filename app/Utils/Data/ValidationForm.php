<?php
namespace Utils\Data;

use Exceptions\UserException;
use Exceptions\ForbiddenException;

class ValidationForm extends Validation
{
    /**
     * Check auth form
     * @param array $data - auth form
     * @return bool
     * @throws UserException|ForbiddenException
     */
    public static function isValidAuthForm(array $data): bool
    {
        return self::isValidEmail($data['email']) && self::isValidPassword($data['password']) && self::isValidCsrf($data['csrf']);
    }

    /**
     * Check register form
     * @param array $data - register form
     * @return bool
     * @throws UserException|ForbiddenException
     */
    public static function isValidRegisterForm(array $data): bool
    {
        return self::isValidLogin($data['login']) && self::isValidEmail($data['email']) && self::isValidPassword($data['password']) &&
            self::isValidPasswordConfirm($data['password'], $data['password_confirm']) && self::isValidCsrf($data['csrf']);
    }

    /**
     * Check recovery form
     * @param array $data - recovery data
     * @return bool
     * @throws UserException|ForbiddenException
     */
    public static function isValidRecoveryEmailForm(array $data): bool
    {
        return self::isValidEmail($data['email']) && self::isValidCsrf($data['csrf']);
    }

    /**
     * Check change password form
     * @param array $data - change data
     * @return bool
     * @throws UserException|ForbiddenException
     */
    public static function isValidRecoveryPasswordForm(array $data): bool
    {
        return self::isValidPassword($data['password']) && self::isValidPassword($data['password_confirm']) &&
            self::isValidPasswordConfirm($data['password'], $data['password_confirm']) && self::isValidCsrf($data['csrf']);
    }
}
