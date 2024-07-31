<?php
namespace Models;

use Models\User\Session;
use ReflectionException;
use System\Auth;
use System\Db;
use Entity\User as EntityUser;
use Models\User\Session as ModelUserSession;

/**
 * Class User
 * @package App\Models
 */
class User extends Model
{
    const PASSWORD_ENTERED = 'Password entered';
    const LOGIN_EXIST = 'This login is already registered';
    const LOGIN_EMPTY = 'This login is empty';
    const EMAIL_EXIST = 'This email is already registered';
    const USER_NOT_SAVED = 'User haven\'t been saved';
    const USER_NOT_CRYPT_KEY = 'Encryption keys weren\'t generated';
    const USER_NOT_WELCOME = 'Greeting wasn\'t sent';
    const USER_NOT_SENT_CONFIRM = 'Verification code wasn\'t sent';
    const USER_NOT_SENT_VERIFY = 'Verification email wasn\'t sent';
    const USER_SOMETHING_WRONG = 'Something went wrong during registration';
    const USER_NOT_FOUND = 'User not found';
    const USER_NOT_ACTIVE = 'This user is not active';
    const USER_BLOCKED = 'This user is blocked';
    const WRONG_PASSWORD = 'Wrong password';
    const WRONG_LOGIN_PASSWORD = 'Wrong login/password';
    const WRONG_USER_DATA = 'Wrong user data';
    const WRONG_TOKEN = 'Wrong token';
    const NOT_AUTHORIZED = 'Not authorized';
    const ALREADY_ACTIVATED = 'This user is already activated';
    const TOO_MANY_FAILED_ATTEMPTS = 'Too many failed attempts';

    protected static $db_table = 'site.users';

    public ?int $id = null;
    public ?int $active = null;
    public ?int $blocked = null;
    public int $group_id = 2;
    public string $login;
    public string $password;
    public ?string $email = null;
    public ?int $show_email = null;
    public ?string $phone = null;
    public ?int $show_phone = null;
    public ?string $name = null;
    public ?string $second_name = null;
    public ?string $last_name = null;
    public int $gender_id = 1;
    public ?int $personal_data_agreement = 1;
    public ?int $mailing = null;
    public int $mailing_type_id = 2;
    public ?int $timezone = null;
    public string $created;
    public ?string $updated = null;

    /**
     * Search user by login
     * @param ?array $params - params
     * @return ?array
     */
    public static function getList(?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $db->params = [];

        if (!empty($params['login'])) {
            $query = "u.id <> :user_id AND u.login LIKE CONCAT('%', :login, '%')";
            $db->params['login'] = $params['login'];
        }
        else
            $query = "u.id <> :user_id";

        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL AND ug.active IS NOT NULL' : '';

        $db->sql = "
            SELECT 
                u.id, u.active, u.blocked, ub.id block_id, u.group_id, u.login, u.password, u.email, u.show_email, 
                u.phone, u.show_phone, u.name, u.second_name, u.last_name, u.gender_id, u.personal_data_agreement, 
                u.mailing, u.mailing_type_id, u.timezone, u.created, u.updated
            FROM {$prefix}{$table} u 
            LEFT JOIN {$prefix}site.user_groups ug ON u.group_id = ug.id 
            LEFT JOIN {$prefix}site.user_blocks ub ON u.id = ub.user_id AND ub.expire > NOW() 
            WHERE {$query} {$active}
            ORDER BY u.login 
            LIMIT 20";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? $data : null;
    }

    /**
     * User by id
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
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL AND ug.active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT 
                u.id, u.active, u.blocked, ub.id block_id, u.group_id, u.login, u.password, u.email, u.show_email, 
                u.phone, u.show_phone, u.name, u.second_name, u.last_name, u.gender_id, u.personal_data_agreement, 
                u.mailing, u.mailing_type_id, u.timezone, u.created, u.updated 
            FROM {$prefix}{$table} u 
            LEFT JOIN {$prefix}site.user_groups ug ON u.group_id = ug.id 
            LEFT JOIN {$prefix}site.user_blocks ub ON u.id = ub.user_id AND ub.expire > NOW() 
            WHERE u.id = :id {$active} 
            ORDER BY ub.expire DESC
            LIMIT 1";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * User by login
     * @param string $login - login
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByLogin(string $login, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL AND ug.active IS NOT NULL' : '';
        $db->params = ['login' => $login];
        $db->sql = "
            SELECT 
                u.id, u.active, u.blocked, ub.id block_id, u.group_id, u.login, u.password, u.email, u.show_email, 
                u.phone, u.show_phone, u.name, u.second_name, u.last_name, u.gender_id, u.personal_data_agreement, 
                u.mailing, u.mailing_type_id, u.timezone, u.created, u.updated   
            FROM {$prefix}{$table} u 
            LEFT JOIN {$prefix}site.user_groups ug ON u.group_id = ug.id 
            LEFT JOIN {$prefix}site.user_blocks ub ON u.id = ub.user_id AND ub.expire > NOW() 
            WHERE u.login = :login {$active}
            ORDER BY ub.expire DESC
            LIMIT 1";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * User by email
     * @param string $email - email
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByEmail(string $email, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL AND ug.active IS NOT NULL' : '';
        $db->params = ['email' => $email];
        $db->sql = "
            SELECT 
                u.id, u.active, u.blocked, ub.id block_id, u.group_id, u.login, u.password, u.email, u.show_email, 
                u.phone, u.show_phone, u.name, u.second_name, u.last_name, u.gender_id, u.personal_data_agreement, 
                u.mailing, u.mailing_type_id, u.timezone, u.created, u.updated 
            FROM {$prefix}{$table} u 
            LEFT JOIN {$prefix}site.user_groups ug ON u.group_id = ug.id 
            LEFT JOIN {$prefix}site.user_blocks ub ON u.id = ub.user_id AND ub.expire > NOW() 
            WHERE LOWER(u.email) = LOWER(:email) {$active}
            ORDER BY ub.expire DESC
            LIMIT 1";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * User by token
     * @param ?string $token - token
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByToken(?string $token, ?array $params = []): ?array
    {
        if ($token === null) return null;

        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL AND ug.active IS NOT NULL' : '';
        $db->params = ['token' => $token];
        $db->sql = "
            SELECT 
                u.id, u.active, u.blocked, ub.id block_id, u.group_id, u.login, u.password, u.email, u.show_email, 
                u.phone, u.show_phone, u.name, u.second_name, u.last_name, u.gender_id, u.personal_data_agreement, 
                u.mailing, u.mailing_type_id, u.timezone, u.created, u.updated 
            FROM {$prefix}{$table} u 
            LEFT JOIN {$prefix}site.user_sessions us ON us.user_id = u.id 
            LEFT JOIN {$prefix}site.user_groups ug ON u.group_id = ug.id 
            LEFT JOIN {$prefix}site.user_blocks ub ON u.id = ub.user_id AND ub.expire > NOW() 
            WHERE us.token = :token {$active}
            ORDER BY ub.expire DESC
            LIMIT 1";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Current user
     * @return ?EntityUser
     * @throws ReflectionException
     */
    public static function getCurrent(): ?EntityUser
    {
        return !empty($_SESSION['user']) ?
            $_SESSION['user'] :
            (self::getToken() ? (new EntityUser())->init(self::getByToken(self::getToken()), ['load_keys' => true]) : null);
    }

    /**
     * Current token
     * @return ?string
     * @throws ReflectionException
     */
    public static function getToken(): ?string
    {
        return !empty($_SESSION['token']) ? $_SESSION['token'] : (!empty($_COOKIE['user']) ? self::getTokenByCookie() : null);
    }

    /**
     * Current token by cookie
     * @return ?string
     * @throws ReflectionException
     */
    private static function getTokenByCookie(): ?string
    {
        $session = Session::getByCookie($_COOKIE['user']);

        if (!empty($session) && !empty($session['token'])) {
            $_SESSION['token'] = $session['token'];
            return self::getToken();
        }
        else {
            Auth::logout();
            return null;
        }
    }

    /**
     * User device
     * @return array
     */
    public static function getDevice(): array
    {
        return [
            'device' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'service' => ModelUserSession::SERVICE_SITE
        ];
    }

    /**
     * Check user auth
     * @return bool
     */
    public static function isAuthorized(): bool
    {
        return Auth::isAuthorizedUser();
    }
}
