<?php
namespace Models\User;

use System\Db;
use Models\Model;

class Gender extends Model
{
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    protected static $db_table = 'site.user_genders';

    public ?int $id = null;
    public ?int $active = 1;
    public string $name;
    public string $created;
    public ?string $updated = null;

    /**
     * Return gender by id
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
        $active = !empty($params['active']) ? 'AND ug.active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT ug.id, ug.active, ug.name, ug.created, ug.updated 
            FROM {$prefix}{$table} ug 
            WHERE ug.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }
}
