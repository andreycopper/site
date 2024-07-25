<?php
namespace Models;

use System\Db;

class IpBlock extends Model
{
    protected static $db_table = 'site.ip_blocks';

    public ?int $id = null;
    public string $ip;
    public ?int $user_id = null;
    public ?string $login = null;
    public string $expire;
    public ?string $reason = null;
    public string $created;
    public ?string $updated = null;

    /**
     * Return ip block by id
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
        $active = !empty($params['active']) ? 'AND ib.expire > NOW()' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT ib.id, ib.ip, ib.user_id, ib.login, ib.expire, ib.reason, ib.created, ib.updated 
            FROM {$prefix}{$table} ib 
            WHERE ib.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return $data ? array_shift($data) : null;
    }

    /**
     * Return ip block by ip
     * @param string $ip - ip
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByIp(string $ip, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND ib.expire > NOW()' : '';
        $db->params = ['ip' => $ip];
        $db->sql = "
            SELECT ib.id, ib.ip, ib.user_id, ib.login, ib.expire, ib.reason, ib.created, ib.updated 
            FROM {$prefix}{$table} ib 
            WHERE ib.ip = :ip {$active}
            ORDER BY ib.expire DESC 
            LIMIT 1";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return $data ? array_shift($data) : null;
    }
}
