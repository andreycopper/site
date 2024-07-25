<?php
namespace Models;

use System\Db;

class TextType extends Model
{
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    protected static $db_table = 'site.text_types';

    public ?int $id = null;
    public ?int $active = 1;
    public string $name;
    public string $created;
    public ?string $updated = null;

    /**
     * Return user session by token
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
        $active = !empty($params['active']) ? 'AND tt.active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT tt.id, tt.active, tt.name, tt.created, tt.updated 
            FROM {$prefix}{$table} tt 
            WHERE tt.id = :id {$active}";
        $data = $db->query();
        return !empty($data) ? array_shift($data) : null;
    }
}
