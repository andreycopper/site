<?php
namespace Models\User;

use System\Db;
use Models\Model;

class Block extends Model
{
    const INTERVAL_MINUTE = 60 * 60;
    const INTERVAL_HOUR = 60 * 60;
    const INTERVAL_DAY = 60 * 60 * 24;
    const INTERVAL_WEEK = 60 * 60 * 24 * 7;
    const INTERVAL_MONTH = 60 * 60 * 24 * 30;
    const INTERVAL_CENTURY = 60 * 60 * 24 * 365 * 100;

    protected static $db_table = 'site.user_blocks';

    public ?int $id = null;
    public int $user_id;
    public string $expire;
    public ?string $reason = null;
    public string $created;
    public ?string $updated = null;

    /**
     * Get block by id
     * @param int $id - block id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getById(int $id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND ub.expire > NOW()' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT ub.id, ub.user_id, ub.expire, ub.reason, ub.created, ub.updated 
            FROM {$prefix}{$table} ub 
            WHERE ub.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Get block by user id
     * @param int $user_id - user id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByUserId(int $user_id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND ub.expire > NOW()' : '';
        $db->params = ['user_id' => $user_id];
        $db->sql = "
            SELECT ub.id, ub.user_id, ub.expire, ub.reason, ub.created, ub.updated 
            FROM {$prefix}{$table} ub 
            WHERE ub.user_id = :user_id {$active} 
            ORDER BY ub.expire DESC 
            LIMIT 1";
        $data = $db->query();
        return !empty($data) ? array_shift($data) : null;
    }
}
