<?php
namespace Models\User;

use DateTime;
use Models\User;
use System\Db;
use Models;
use Models\Model;
use ReflectionException;
use Exceptions\DbException;
use System\Loggers\ErrorLogger;
use System\Loggers\AccessLogger;
use Entity\User\Session as UserSession;

class Session extends Model
{
    const SERVICE_MOBILE = 1;
    const SERVICE_SITE = 2;
    const SERVICES = [self::SERVICE_MOBILE, self::SERVICE_SITE];

    protected static $db_table = 'site.user_sessions';

    public ?int $id;
    public ?int $active = 1;
    public string $email;
    public int $user_id;
    public int $service_id = 2;
    public string $ip;
    public string $device;
    public string $log_on;
    public string $expire;
    public ?string $token = null;
    public ?string $comment = null;

    /**
     * Return user session by id
     * @param int $id - id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getById(int $id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND us.active IS NOT NULL AND us.expire > NOW()' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT 
                us.id, us.active, us.email, us.user_id, us.service_id, us.ip, us.device, us.log_on, us.expire, us.token, us.comment  
            FROM {$prefix}{$table} us 
            WHERE us.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Return user session by token
     * @param ?string $token - token
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByToken(?string $token, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND us.active IS NOT NULL AND us.expire > NOW()' : '';
        $db->params = ['token' => $token];
        $db->sql = "
            SELECT 
                us.id, us.active, us.email, us.user_id, us.service_id, us.ip, us.device, us.log_on, us.expire, us.token, us.comment 
            FROM {$prefix}{$table} us 
            WHERE us.token = :token {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Count of failed auth attempts of last day
     * @param $login - login
     * @param $ip - ip
     * @return array
     * @throws DbException
     */
    public static function getFailedAttempts($email, $ip): array
    {
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $db->params = ['email' => $email, 'ip' => $ip];
        $db->sql = "
            WITH q AS (
                SELECT id, active, email, ip, device, log_on, token, comment
                FROM {$prefix}{$table}
                WHERE email = :email AND IFNULL(token, '') = '' AND log_on > DATE_SUB(NOW(), INTERVAL 1 DAY) AND active IS NOT NULL
            ),
            login AS (
                SELECT count(q.id) count FROM q
            ),
            ip AS (
                SELECT count(q.id) count FROM q WHERE q.ip = :ip
            )
            SELECT login.count total, ip.count by_ip FROM login, ip";
        $res = $db->query();

        return array_shift($res);
    }

    /**
     * Clear failed auth attempts
     * @param $email - login
     * @return ?array
     * @throws DbException
     */
    public static function clearFailedAttempts($email): ?array
    {
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = new Db();
        $db->params = ['email' => $email];
        $db->sql = "
            UPDATE {$prefix}{$table} 
            SET active = NULL 
            WHERE email = :email AND IFNULL(token, '') = '' AND active IS NOT NULL";

        return $db->query();
    }

    /**
     * Deactivate current user session
     * @return bool
     * @throws ReflectionException
     */
    public static function deleteCurrent(): bool
    {
        $userSession = UserSession::factory(['token' => User::getToken()]);
        if (!empty($userSession) && !empty($userSession->getId())) {
            $userSession->setActive(false);
            $userSession->setExpire((new DateTime())->modify('-1 hour'));

            if ($userSession->save()) {
                AccessLogger::getInstance()->info("Пользователь {$userSession->getEmail()} разлогинен. UserId: {$userSession->getUser()->getId()}.");
                return true;
            } else {
                ErrorLogger::getInstance()->error("Не удалось удалить сессию пользователя {$userSession->getEmail()}. UserId: {$userSession->getUser()->getId()}.");
                return false;
            }
        }

        return true;
    }
}
