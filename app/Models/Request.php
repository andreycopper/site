<?php
namespace Models;

use System\Db;

abstract class Request extends Model
{
    const ACTION_ACCEPT = 'accept';
    const ACTION_DECLINE = 'decline';
    const ACTION_CANCEL = 'cancel';

    const CANT_WRITE_YOURSELF = 'You can\'t write to yourself';
    const NEED_REQUEST = 'Need a request to communicate with user';
    const USER_DECLINED = 'User declined your request';
    const SELF_DECLINED = 'You declined user\'s request';
    const USER_NOT_ACCEPTED = 'User has not accepted your request yet';
    const SELF_NOT_ACCEPTED = 'You have not accepted user\'s request yet';

    public ?int $id = null;
    public int $user_id;
    public int $request_type_id;
    public int $to_user_id;
    public ?string $accepted = null;
    public ?string $declined = null;
    public string $created;
    public ?string $updated = null;

    /**
     * Request ids list
     * @param ?array $params - params
     * @return ?array
     */
    public static function getList(?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false, 'sort' => 'created', 'order' => 'DESC', 'limit' => 100];
        $prefix = self::$db_prefix;
        $table = static::$db_table;

        $db = Db::getInstance();
        $accepted = !empty($params['accepted']) ? 'AND r.accepted IS NOT NULL' : '';
        $requested = !empty($params['requested']) ? 'AND r.accepted IS NULL' : '';
        $db->params = ['user_id' => $params['user_id']];
        $db->sql = "
            SELECT 
                r.id, r.user_id, u1.login user_login, r.to_user_id, u2.login to_user_login, 
                r.accepted, r.declined, r.created, r.updated 
            FROM {$prefix}{$table} r 
            JOIN {$prefix}site.users u1 ON u1.id = r.user_id
            JOIN {$prefix}site.users u2 ON u2.id = r.to_user_id
            WHERE (r.user_id = :user_id OR r.to_user_id = :user_id) {$accepted} {$requested}
            ORDER BY r.{$params['sort']} {$params['order']} 
            LIMIT {$params['limit']}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return $data ?? null;
    }

    /**
     * Return request by id
     * @param int $id - id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getById(int $id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = static::$db_table;

        $db = Db::getInstance();
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT 
                r.id, r.user_id, u1.login user_login, r.to_user_id, u2.login to_user_login, 
                r.accepted, r.declined, r.created, r.updated 
            FROM {$prefix}{$table} r 
            JOIN {$prefix}site.users u1 ON u1.id = r.user_id
            JOIN {$prefix}site.users u2 ON u2.id = r.to_user_id
            WHERE r.id = :id";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Return request by users' ids
     * @param int $user_id - first user id
     * @param int $to_user_id - second user id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByUsersId(int $user_id, int $to_user_id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = static::$db_table;

        $db = Db::getInstance();
        $db->params = ['user_id' => $user_id, 'to_user_id' => $to_user_id];
        $db->sql = "
            SELECT 
                r.id, r.user_id, u1.login user_login, r.to_user_id, u2.login to_user_login, 
                r.accepted, r.declined, r.created, r.updated 
            FROM {$prefix}{$table} r 
            JOIN {$prefix}site.users u1 ON u1.id = r.user_id
            JOIN {$prefix}site.users u2 ON u2.id = r.to_user_id
            WHERE r.user_id = :user_id AND r.to_user_id = :to_user_id";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }
}
