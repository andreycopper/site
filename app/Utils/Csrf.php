<?php
namespace Utils;

use System\Token;

class Csrf
{
    /**
     * Return csrf
     * @return string
     */
    public static function get(): string
    {
        $csrf = $_SESSION['csrf'] ?? null;

        if (empty($csrf['token']) || empty($csrf['salt']) || empty($csrf['secret']) || $csrf['expire'] < time())
            $csrf = self::generate();

        return $csrf['token'];
    }

    /**
     * Generate data for csrf
     * @return array
     */
    public static function generate(): array
    {
        $session_id = session_id();
        $login = !empty($_SESSION['user']) && !empty($_SESSION['user']->getId()) ? $_SESSION['user']->getLogin() : 'unknown';

        $_SESSION['csrf']['salt'] = md5(time());
        $_SESSION['csrf']['secret'] = sha1(time());
        $_SESSION['csrf']['token'] = sha1("{$_SESSION['csrf']['salt']}:{$_SESSION['csrf']['secret']}:{$session_id}:{$login}");
        $_SESSION['csrf']['expire'] = time() + Token::TOKEN_LIFE_TIME;
        return $_SESSION['csrf'];
    }
}
