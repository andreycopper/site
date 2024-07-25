<?php
namespace Models;

use System\Db;

class Bookmark extends Model
{
    const ACTION_REMOVE = 'remove';

    protected static $db_table = 'site.bookmarks';

    public ?int $id = null;
    public int $user_id;
    public int $mark_user_id;
    public string $created;
    public ?string $updated = null;

    /**
     * Return bookmark list
     * @param ?array $params - params
     * @return ?array
     */
    public static function getList(?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL' : '';
        $db->params = ['user_id' => $params['user_id']];
        $db->sql = "
            SELECT 
                ub.id, ub.user_id, ub.mark_user_id, ur1.id request_out_id, ur2.id request_in_id, ub.created, ub.updated 
            FROM {$prefix}{$table} ub 
            JOIN {$prefix}site.users u ON u.id = ub.mark_user_id 
            LEFT JOIN {$prefix}site.user_requests ur1 ON ur1.user_id = ub.user_id AND ur1.to_user_id = ub.mark_user_id
            LEFT JOIN {$prefix}site.user_requests ur2 ON ur2.user_id = ub.mark_user_id AND ur2.to_user_id = ub.user_id
            WHERE ub.user_id = :user_id {$active} AND ub.mark_user_id <> :user_id";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return $data ?? null;
    }

    /**
     * Return bookmark by id
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
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT 
                ub.id, ub.user_id, ub.mark_user_id, 
                ur1.created out_created, ur1.accepted out_accepted, ur1.declined out_declined,
                ur2.created in_created, ur2.accepted in_accepted, ur2.declined in_declined,
                ub.created, ub.updated 
            FROM {$prefix}{$table} ub 
            JOIN {$prefix}site.users u ON u.id = ub.mark_user_id 
            LEFT JOIN {$prefix}site.user_requests ur1 ON ur1.user_id = ub.user_id AND ur1.to_user_id = ub.mark_user_id
            LEFT JOIN {$prefix}site.user_requests ur2 ON ur2.user_id = ub.mark_user_id AND ur2.to_user_id = ub.user_id
            WHERE ub.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Return bookmark by users id
     * @param int $user_id - user id
     * @param int $mark_user_id - mark user id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByUsersId(int $user_id, int $mark_user_id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND u.active IS NOT NULL' : '';
        $db->params = ['user_id' => $user_id, 'mark_user_id' => $mark_user_id];
        $db->sql = "
            SELECT 
                ub.id, ub.user_id, ub.mark_user_id, 
                ur1.created out_created, ur1.accepted out_accepted, ur1.declined out_declined,
                ur2.created in_created, ur2.accepted in_accepted, ur2.declined in_declined,
                ub.created, ub.updated 
            FROM {$prefix}{$table} ub 
            JOIN {$prefix}site.users u ON u.id = ub.mark_user_id 
            LEFT JOIN {$prefix}site.user_requests ur1 ON ur1.user_id = ub.user_id AND ur1.to_user_id = ub.mark_user_id
            LEFT JOIN {$prefix}site.user_requests ur2 ON ur2.user_id = ub.mark_user_id AND ur2.to_user_id = ub.user_id
            WHERE ub.user_id = :user_id AND ub.mark_user_id = :mark_user_id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }
}
