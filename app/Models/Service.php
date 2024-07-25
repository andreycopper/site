<?php
namespace Models;

use System\Db;

class Service extends Model
{
    protected static $db_table = 'site.services';

    public ?int $id = null;
    public ?int $active = 1;
    public string $name;
    public string $created;
    public ?string $updated = null;

    /**
     * Return
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
        $active = !empty($params['active']) ? 'AND s.active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT s.id, s.active, s.name, s.created, s.updated 
            FROM {$prefix}{$table} s 
            WHERE s.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }
}
