<?php
namespace Models;

use System\Db;
use Exceptions\DbException;

class Page extends Model
{
    protected static $db_table = 'pages';

    /**
     * Получает информацию по текущей странице
     * @param string $class
     * @param bool $active
     * @param bool $object
     * @return false|mixed
     * @throws DbException
     */
    public static function getPageInfo(string $class, bool $active = true, $object = true)
    {
        $page = explode('\\', mb_strtolower($class));
        $page = array_pop($page);

        $activity = !empty($active) ? 'AND p.active IS NOT NULL' : '';
        $sql = "SELECT * FROM pages p WHERE p.link = :page {$activity}";
        $params = [
            ':page' => $page
        ];
        $db = new Db();
        $data = $db->query($sql, $params,$object ? static::class : null);

        if (!empty($data)) $res = $_SESSION['page'] = array_shift($data);
        return $res ?? false;
    }

    /**
     * Находит и возвращает активные записи из БД и формирует иерархическое меню
     * @param bool $active
     * @param bool $object
     * @param string $orderBy
     * @param string $order
     * @return array|bool
     * @throws DbException
     */
    public static function getMenuTree($active = false, $object = false, $orderBy = 'sort', $order = 'ASC')
    {
        $activity = !empty($active) ? 'AND p.active IS NOT NULL' : '';
        $sql = "
            SELECT p.id, p.menu, p.footer, p.parent_id, p.name, p.link, p.description, p.meta_d, p.meta_k, p.sort 
            FROM pages p 
            WHERE p.menu IS NOT NULL {$activity} 
            ORDER BY {$orderBy} {$order}";
        $db = new Db();
        $data = $db->query($sql, [],$object ? static::class : null);
        $res = [];

        if (!empty($data)) {
            foreach ($data as $item) {
                if (empty($item['parent_id'])) $res[0][$item['id']] = $item;
                else $res[$item['parent_id']][$item['id']] = $item;
            }
        }
        if (!empty($res)) $_SESSION['menu'] = $res;
        return $res ?? false;
    }
}