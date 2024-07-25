<?php
namespace Models;

use System\Db;

class Album extends Model
{
    const ALBUM_MESSAGE = 'message';
    const ALBUM_PROFILE = 'profile';
    const ALBUM_EMPTY = 'Album is empty';
    const ALBUM_NOT_FOUND = 'Album not found';

    protected static $db_table = 'site.albums';

    public ?int $id = null;
    public ?int $active = 1;
    public int $user_id;
    public ?string $section = null;
    public ?string $name = null;
    public string $created;
    public ?string $updated = null;

    /**
     * Return album by id
     * @param int $id - album id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getById(int $id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND a.active IS NOT NULL' : '';
        $db->params = ['id' => $id];

        $db->sql = "
            SELECT *
            FROM {$prefix}{$table} a 
            WHERE a.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return $data ? array_shift($data) : null;
    }
}
