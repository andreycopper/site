<?php
namespace Utils\Data;

use Utils\Csrf;
use Exceptions\UserException;
use Exceptions\ForbiddenException;

class Validation
{
    const USER_LOGIN = '/^[A-Za-z][A-Za-z0-9_-]{2,}$/';
    const USER_EMAIL = '/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,6}$/i';
    const USER_PASSWORD = '/((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,})/';
    //const USER_PASSWORD = '/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}/i';
    const USER_PHONE = '/^\+?[78]?[ -]?[(]?9\d{2}\)?[ -]?\d{3}-?\d{2}-?\d{2}$/i';

    const ACCESS_DENIED = 'Access denied';
    const NOT_ALLOWED_ACTION = 'Not allowed action';
    const ID_EMPTY = 'ID empty';
    const WRONG_DATA_TYPE = 'Wrong data type';

    const REASON = 'Reason';
    const WRONG_TOKEN = 'Wrong token';
    const EMAIL_INCORRECT = 'Incorrect email';
    const USER_ACTIVE = 'User is already active';
    const USER_NOT_ACTIVE = 'User is not active';
    const USER_NOT_FOUND = 'User is not found';
    const USER_IS_BLOCKED = 'User is blocked';
    const USER_TEMPORARILY_BLOCKED = 'User temporarily blocked';
    const USER_TEMPORARILY_BLOCKED_TILL = 'User temporarily blocked till ';
    const USER_IP_TEMPORARILY_BLOCKED_TILL = 'User ip temporarily blocked till ';
    const TOO_MANY_FAILED_ATTEMPTS = 'Too many failed auth attempts';
    const TOO_MANY_FAILED_ATTEMPTS_BY_LOGIN = 'Too many failed auth attempts by login';
    const TOO_MANY_FAILED_ATTEMPTS_BY_IP = 'Too many failed auth attempts by ip';
    const FORM_NOT_MATCH_REQUIREMENTS = 'Form doesn\'t match security requirements';
    const QUERY_EMPTY = 'Search query is empty';
    const QUERY_SHORT = 'Search query is too short';
    const LOGIN_EMPTY = 'Login is empty';
    const EMAIL_EMPTY = 'Email is empty';
    const LOGIN_NOT_MATCH_REQUIREMENTS = 'Login doesn\'t match security requirements';
    const PASSWORD_EMPTY = 'Password is empty';
    const PASSWORD_NOT_MATCH_CONFIRMATION = 'Password doesn\'t match confirmation';
    const PASSWORD_NOT_MATCH_REQUIREMENTS = 'Password doesn\'t match security requirements';
    const PASSWORD_NO_UPPER_LETTERS = 'Password doesn\'t contain capital letters';
    const PASSWORD_NO_LOWER_LETTERS = 'Password doesn\'t contain lowercase letters';
    const PASSWORD_NO_NUMBERS = 'Password doesn\'t contain numbers';
    const PASSWORD_NO_SPECIAL_CHARACTERS = 'Password doesn\'t contain special characters';

    /**
     * @param string $pattern - regexp
     * @param string $subject - object
     * @return bool
     */
    public static function check(string $pattern, string $subject): bool
    {
        return !empty($subject) && preg_match($pattern, $subject);
    }

    /**
     * @param ?string $id - id
     * @return bool
     * @throws UserException
     */
    public static function isValidId(?string $id): bool
    {
        if (empty($id)) throw new UserException(self::ID_EMPTY);
        if (!is_numeric($id)) throw new UserException(self::WRONG_DATA_TYPE);
        return true;
    }

    /**
     * Check login
     * @param string $login - login
     * @return bool
     * @throws UserException
     */
    public static function isValidLogin(string $login): bool
    {
        if (empty($login)) throw new UserException(self::LOGIN_EMPTY);
        if (!self::check(self::USER_LOGIN, $login)) throw new UserException(self::LOGIN_NOT_MATCH_REQUIREMENTS);
        return true;
    }

    /**
     * Check email
     * @param string $email - email
     * @return bool
     * @throws UserException
     */
    public static function isValidEmail(string $email): bool
    {
        if (empty($email)) throw new UserException(self::EMAIL_EMPTY);
        if (!self::check(self::USER_EMAIL, $email)) throw new UserException(self::EMAIL_INCORRECT);
        return true;
    }

    /**
     * Check password
     * @param string $password - password
     * @return bool
     * @throws UserException
     */
    protected static function isValidPassword(string $password): bool
    {
        if (empty($password)) throw new UserException(self::PASSWORD_EMPTY);
        if (!self::check(self::USER_PASSWORD, $password)) throw new UserException(self::PASSWORD_NOT_MATCH_REQUIREMENTS);
        return true;
    }

    /**
     * Check password
     * @param string $password - password
     * @param string $passwordConfirm - password confirm
     * @return bool
     * @throws UserException
     */
    protected static function isValidPasswordConfirm(string $password, string $passwordConfirm): bool
    {
        self::isValidPassword($passwordConfirm);
        if ($password !== $passwordConfirm) throw new UserException(self::PASSWORD_NOT_MATCH_CONFIRMATION);
        return true;
    }

    /**
     * Check csrf
     * @param ?string $csrf - csrf
     * @return bool
     * @throws ForbiddenException
     */
    public static function isValidCsrf(?string $csrf = null): bool
    {
        if (empty($csrf) || $csrf !== Csrf::get()) throw new ForbiddenException(self::FORM_NOT_MATCH_REQUIREMENTS);
        return true;
    }

    /**
     * Check phone number
     * @param string $phone - phone
     * @return bool
     */
    public static function isValidPhone(string $phone): bool
    {
        return !empty($phone) && mb_strlen($phone) > 9 && mb_strlen($phone) < 20 && preg_match(self::USER_PHONE, $phone);
    }
}
