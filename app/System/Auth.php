<?php
namespace System;

use Entity;
use Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Utils\Data;
use Entity\User\Session;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\UserException;
use System\Loggers\AccessLogger;
use Exceptions\ForbiddenException;
use Models\User\Session as ModelUserSession;
use Utils\Data\ValidationUser;
use Utils\Data\ValidationAuth;
use Models\User as ModelUser;

class Auth {
    const MAX_TOTAL_FAILED_ATTEMPTS = 10;
    const MAX_FAILED_ATTEMPTS_BY_IP = 5;
    const PASSWORD_ENTERED = 'Password entered';
    const USER_NOT_FOUND = 'User not found';
    const WRONG_PASSWORD = 'Wrong password';
    const WRONG_LOGIN_PASSWORD = 'Wrong login/password';
    const WRONG_USER_DATA = 'Wrong user data';
    const WRONG_TOKEN = 'Wrong token';
    const NOT_AUTHORIZED = 'Not authorized';
    const TOO_MANY_FAILED_ATTEMPTS = 'Too many failed auth attempts';

    private string $email;
    private string $password;
    private bool $remember = false;
    private ?User $user = null;
    private ?Session $session = null;

    /**
     * @param string $email
     * @param string $password
     * @param bool $remember
     */
    public function __construct(string $email, string $password, bool $remember = false)
    {
        $this->email = $email;
        $this->password = $password;
        $this->remember = $remember;
    }

    /**
     * Auth
     * @throws DbException|UserException|ReflectionException|ForbiddenException
     */
    public function auth(): void
    {
        $this->user = User::factory(['email' => $this->email, 'load_keys' => true]);
        ValidationUser::isValidActiveUser($this->user);

        if (!password_verify($this->password, $this->user->getPassword())) {
            (new Session($this->user->getId(), $this->user->getEmail(), self::WRONG_PASSWORD))->save();
            throw new ForbiddenException(self::WRONG_PASSWORD, 401);
        }

        $this->login();
    }

    /**
     * Login
     * @throws DbException|ReflectionException
     */
    public function login(): void
    {
        $this->session = new Session($this->user->getId(), $this->user->getEmail(), self::PASSWORD_ENTERED);
        $this->session->setToken(Token::get($this->session));
        if ($this->remember)
            $this->session->setCookie(hash('sha512', $this->user->getEmail() . $this->user->getCreated()->format('Y.m.d H:i:s') . time()));
        $this->session->save();

        ModelUserSession::clearFailedAttempts($this->user->getEmail());
        AccessLogger::getInstance()->info("Пользователь {$this->session->getEmail()} залогинен. UserId: {$this->session->getUser()->getId()}. Device: {$this->session->getDevice()}.");

        $_SESSION['token'] = $this->session->getToken();
        $_SESSION['user'] = $this->user;
        if ($this->remember) setcookie('user', $this->session->getCookie(), time() + Token::TOKEN_LIFE_TIME, '/', DOMAIN, 0);

        if (Request::isAjax()) Response::result();
        else {
            header('Location: /');
            die;
        }
    }

    /**
     * Check auth
     * @return bool
     */
    public static function isAuthorizedUser(): bool
    {
        try {
            $token = ModelUser::getToken();
            $userData = ModelUser::getDevice();
            if (!ValidationAuth::isValidToken($token) || !ValidationAuth::isValidUserData($userData)) return false;

            $userSession = Session::factory(['token' => $token]);
            if (!ValidationAuth::isValidSession($userSession)) return false;

            $user = !empty($_SESSION['user']) ? $_SESSION['user'] : User::factory(['id' => $userSession ?-> getUser()->getId()]);
            if (empty($user->getId())) return false;

            $jwt = JWT::decode($token, new Key(Token::KEY, 'HS512'));
            if (!ValidationAuth::isValidJwt($jwt) || !ValidationAuth::isValidTokenData($jwt, $userData) || !ValidationAuth::isValidTokenSession($jwt, $userSession))
                return false;

            if (empty($_SESSION['user'])) $_SESSION['user'] = $user;
            return true;
        }
        catch (\Exception) {
            return false;
        }
    }

    /**
     * Logout
     * @throws ReflectionException
     */
    public static function logout(): void
    {
        ModelUserSession::deleteCurrent();
        unset($_SESSION['token']);
        unset($_SESSION['user']);
        setcookie('user', '', time() - Token::TOKEN_LIFE_TIME, '/', DOMAIN, 0);
        setcookie('PHPSESSID', '', time() - Token::TOKEN_LIFE_TIME, '/', DOMAIN, 0);
        session_destroy();
    }
}
