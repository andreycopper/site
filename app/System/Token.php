<?php
namespace System;

use Firebase\JWT\JWT;
use Entity\User\Session;
use Models\User\Session as ModelUserSession;

class Token {
    const TOKEN_LIFE_TIME = 60 * 60 * 24 * AUTH_DAYS;
    const KEY = 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDlVR4kjJJIPOLS'.
    'dZPjpyc9OhdJALG5U+5t7ZlwGjs0XLpYXVH+up/LBaNn9bXSSLXHmHS+kRx0lQ2s'.
    'WJt3DLMPWeLmqBFafiMqJTPMH0MOG03GOs5ysIOnBfglrF7wsVdbJ0eLY2pn4FSb'.
    'R8DD3gG8lKc5lKsX/YQpJpMY9ScyRkej78OJgV8OSFz2uXquleQYBIqJiXlBRX+x'.
    'rdA7gba4OBQ4xopHpPiFzWqi7wpyEHJwrSKfACddxt3SoDfcSas7qHT8cYR/7FfW'.
    'qK4leqOr+4IIWIwg+SHjXn/nI9p8Yl9pbQRu3wldb60I5kMx1ttG5JnXVDS2Xa4N'.
    'ou4yqKxTAgMBAAECggEAban9Rwb9cJPE91UISkWFSw77UYqGchQDoQeMZwH0K77n'.
    '0ReW/RNrFB93G2Bw+MSSQIbT1Mj5bXpVVoPsm8oz2fRmv1vBKJ/G2wF9Om/8lcNS'.
    'tqcqT8e+vODq38EEiV6UYl9vI+V/XXr1mmd+c2s3M2F2cd94zXkKeM8cakPY4X24'.
    'SEcfAxSdiYyS2jaUrleXnkH8P5n9KB2svKc0YOrUPoYQN3qCMHtwVj/ooJGBihJi'.
    'hTI4eqnbzGFB7BhhvyLQFfynTfGVmjOuRe+edfjB/XMpRom33FLo3/jPGQfTZyf1'.
    'GMa9ZX2UX/W85JJBCu5zBIdDvFpa2cxlTG/dthJfYQKBgQD0fnzUg53W4+rKQ7sy'.
    'IiDiEWore1BZ34u3AhBu/v2CBD36fAK0r5620LuO2Ivt9vGlr5+Zd+SsimEHbnjs'.
    '/Cj0STijhK2ChYnSTHE4N+jWTiCtyHDwdx8GzeynwNWYEBm77QernxnlqmxFVQyy'.
    'bZ2f+eef2EWb+kwiFubnPhQcIwKBgQDwH/j9CfW6DwxhdbU5HEHbCaMIwZk0ibRz'.
    'cUvEBzBxPRhHKMY0vDTxRbYoR1n0Bk7aBggjjQznKm9qu4pGOz7W0Jy4KhT2rrUW'.
    'W77e29dy/GzQl62GbKdX+Xgof6XVO00SeglVNyl19lKgbo+7QCW90BPnbpFT0FMy'.
    'tJUs6n/aEQKBgE2Imy+NeY1/A6MW7ZNNbV4jpaaaWXXWxWjI18tLQ7tqevknQHhq'.
    'RQu2j/QRyfYx3JntYtB5S+RHkdOYffKxWI93dOWuGpQMoxM0uKbaXBUx+30A1of7'.
    'TrSKsyTTqyio2nBVD5ymPMEvVVx7RyaCSn1D8+cl35VCP8iDuL/WWOw/AoGAAr6K'.
    'zPGJMmgrnw2GVbHB+uvgRiBYTOZp/ovD7uiaICEvntiTc1TXUg6W51zxZT2RNyKs'.
    'gyOAiz/L6C9ehDD3JeNadyxb8vnKLgg8ZTWj/7ds0vF41Tl2rCW4vtW+onI8DkMA'.
    'yk2IFsYK6bR5xg0UoVe4coOr+4Y8/S5dKhdAANECgYB/jV9X4ADSzwN8nL4EX/fQ'.
    'TsvafM8+UM6dtRGWYP0PtyKTpSNh1RqH48WgehFd5EkXR/hiS06GwIytzV+CzdXV'.
    'VKknIMRiGUz5hbEPGs1uApuQ3iCPgvf0Rr1H0pFbZE9A10pDfuHkYzT+SykFa/dk'.
    '8rhor/JbMwB7p2rnj4mpfQ==';

    /**
     * User token
     * @param Session $session - user session
     * @return string
     */
    public static function get(Session $session): string
    {
        $data = [
            "iss" => SITE_URL,                                  // адрес или имя удостоверяющего центра
            "aud" => $session->getEmail(),                  // имя клиента для которого токен выпущен
            "iat" => $session->getLogOn()->getTimestamp(),  // время, когда был выпущен JWT
            "nbf" => $session->getLogOn()->getTimestamp(),  // время, начиная с которого может быть использован (не раньше, чем)
            "exp" => $session->getExpire()->getTimestamp(), // время истечения срока действия токена
            "data" => [
                "user"    => $session->getEmail(),
                "service" => ModelUserSession::SERVICE_SITE,
                "ip"      => $_SERVER['REMOTE_ADDR'],
                "device"  => $_SERVER['HTTP_USER_AGENT'],
                "expired" => $session->getExpire()->getTimestamp()
            ]
        ];

        return JWT::encode($data, self::KEY, 'HS512');
    }
}
