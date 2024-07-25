<?php
namespace Utils\Data;

use DateTime;
use Entity\User\Session;
use Exceptions\ForbiddenException;

class ValidationAuth extends Validation
{
    /**
     * Check token
     * @param ?string $token
     * @return bool
     */
    public static function isValidToken(?string $token): bool
    {
        return !empty($token);
    }

    /**
     * Check user data
     * @param ?array $userData
     * @return bool
     */
    public static function isValidUserData(?array $userData): bool
    {
        return !empty($userData['device']) && !empty($userData['ip']) && !empty($userData['service']);
    }

    /**
     * Check user session
     * @param ?Session $session
     * @return bool
     */
    public static function isValidSession(?Session $session): bool
    {
        return !empty($session) && !empty($session->getId()) && !empty($session->getUser())&& !empty($session->getUser()->getId());
    }

    /**
     * Check JWT token
     * @param ?Object $token - token
     * @return bool
     * @throws ForbiddenException
     */
    public static function isValidJwt(?Object $token): bool
    {
        $now = (new DateTime())->getTimestamp();
        if (empty($token) || $token->exp < $now || $token->iat > $now || $token->nbf > $now)
            throw new ForbiddenException(self::WRONG_TOKEN);

        return true;
    }

    /**
     * Check user data in token
     * @param Object $token - token
     * @param array $userData - user data
     * @return bool
     */
    public static function isValidTokenData(Object $token, array $userData): bool
    {
        return $userData['device'] === $token->data->device && $userData['ip'] === $token->data->ip && $userData['service'] === $token->data->service;
    }

    /**
     * Check session in token
     * @param Object $token - token
     *  @param Session $session - user session
     * @return bool
     */
    public static function isValidTokenSession(Object $token, Session $session): bool
    {
        return
            $token->iss === SITE_URL && $token->aud === $session->getEmail() &&
            $token->data->user === $session->getEmail() && $token->data->service === $session->getService()->getId() &&
            $token->data->ip === $session->getIp() && $token->data->device === $session->getDevice();
    }
}
