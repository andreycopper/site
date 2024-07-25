<?php
namespace Models\User;

use System\Db;
use Models\Model;

class EventTemplate extends Model
{
    protected static $db_table = 'site.user_event_templates';

    public ?int $id = null;
    public ?int $active = null;
    public int $user_event_type_id;
    public string $name;
    public string $message;
    public string $created;
    public ?string $updated = null;

    /**
     * Get event template by id
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
        $active = !empty($params['active']) ? 'AND uet.active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT uet.id, uet.active, uet.user_event_type_id, uet.name, uet.message, uet.created, uet.updated 
            FROM {$prefix}{$table} uet 
            WHERE uet.id = :id {$active}";
        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }
}