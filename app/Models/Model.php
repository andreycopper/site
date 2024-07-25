<?php
namespace Models;

use DateTime;
use System\Db;
use Traits\Magic;
use Entity\Entity;
use ReflectionClass;
use ReflectionException;

/**
 * Class Model
 * @package App\Models
 */
abstract class Model
{
    protected static $db_prefix = CONFIG['db']['dbprefix'];
    protected static $db_table = null;

    use Magic;

    /**
     * Init model from entity
     * @param Entity|null $data - объект entity
     * @return ?static
     * @throws ReflectionException
     */
    public function init(?Entity $data): ?static
    {
        if (empty($data)) return null;

        $fields = $data->getFields();
        $reflectionClass = new ReflectionClass($data);

        foreach ($fields as $key => $field) {
            if (!property_exists($this, $key)) continue;

            $type = $field['type'];
            $prop = $field['field'];

            $property = $reflectionClass->getProperty($prop);
            $property->setAccessible(true);
            $value = $property->getValue($data);

            if (is_null($value) || $value === false) $this->$key = null;
            else $this->$key = $this->convertValue($type, $value);
        }

        return $this;
    }

    /**
     * Convert value
     * @param $type - var type
     * @param $value - value
     * @return mixed
     */
    private function convertValue($type, $value): mixed
    {
        switch ($type) {
            case 'int':
            case 'bool':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'datetime':
                return $value instanceof DateTime ? $value->format('Y-m-d H:i:s') : $value;
            case 'array':
                return json_encode($value);
            default:
                if (class_exists("Entity\\$type") && $value instanceof Entity) {
                    $value = $value->getId();
                }

                return $value;
        }
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $key => $value) {
            if ($key === 'data') continue;
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Находит и возвращает записи из БД
     * @param $params
     * $params['active'] - только активные сообщения
     * $params['sort'] - поле сортировки
     * $params['order'] - направление сортировки
     * $params['limit'] - лимит сообщений для выдачи
     * @return array|bool
     */
    public static function getList(?array $params = [])
    {
        $params += ['active' => true, 'object' => false];
        $prefix = static::$db_prefix;
        $table = static::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'WHERE active IS NOT NULL' : '';
        $sort = !empty($params['sort']) ? $params['sort'] : 'id';
        $order = !empty($params['order']) ? strtoupper($params['order']) : 'ASC';
        $limit = !empty($params['limit']) ? "LIMIT {$params['limit']}" : '';

        $db->sql = "
            SELECT * 
            FROM {$prefix}{$table} 
            {$active} 
            ORDER BY {$sort} {$order} 
            {$limit}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return $data ?? false;
    }

    /**
     * Get model by id
     * @param int $id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getById(int $id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = static::$db_prefix;
        $table = static::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND active IS NOT NULL' : '';
        $db->params = ['id' => $id];
        $db->sql = "SELECT * FROM {$prefix}{$table} WHERE id = :id {$active}";
        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Сохраняет запись в БД
     * @return bool|int
     */
    public function save()
    {
        return $this->isNew() ? $this->insert() : $this->update();
    }

    /**
     * Проверяет добавляется новый элемент или редактируется существующий
     * @return bool
     */
    public function isNew(): bool
    {
        return !(!empty($this->id) && !empty(self::getById($this->id, ['active' => false])));
    }

    /**
     * Добавляет запись в БД
     * @return bool|int
     */
    public function insert()
    {
        $db = Db::getInstance();
        $cols = [];
        $db->params = [];
        foreach ($this as $key => $val) {
            if ($val === null) continue;
            if ($key === 'data') continue;
            $cols[] = $key;
            $db->params[$key] = $val;
        }
        $db->sql =  "
            INSERT INTO " . self::$db_prefix . static::$db_table . " (" . implode(', ', $cols) . ") 
            VALUES (" . ":" . implode(', :', $cols) . ")";

        $res = $db->execute();
        return !empty($res) ? $db->lastInsertId() : false;
    }

    /**
     * Обновляет запись в БД
     */
    public function update()
    {
        $db = Db::getInstance();
        $binds = [];
        $db->params = [];
        foreach ($this as $key => $val) {
            //if ($val === null) continue;
            if ($key === 'data') continue;
            if ('id' !== $key) $binds[] = $key . ' = :' . $key;
            $db->params[$key] = $val;
        }
        $db->sql = 'UPDATE ' . self::$db_prefix . static::$db_table . ' SET ' . implode(', ', $binds) . ' WHERE id = :id';

        return $db->execute() ? $this->id : false;
    }

    /**
     * Удаляет запись из БД
     * @return bool
     */
    public function delete(): bool
    {
        $db = Db::getInstance();
        $db->params = [':id' => $this->id];
        $db->sql = "DELETE FROM " . self::$db_prefix . static::$db_table . " WHERE id = :id";
        return $db->execute();
    }

    /**
     * Возвращает количество записей в таблице
     * @return bool|int
     */
    public static function count()
    {
        $db = Db::getInstance();
        $db->sql = "SELECT COUNT(*) count FROM " . self::$db_prefix . static::$db_table;
        $data = $db->query(static::class);
        return !empty($data) ? (int)array_shift($data)->count : false;
    }

    /**
     * Заполняет поля модели данными из массива
     * Запускает метод обработки даного поля, если он существует
     * @param array $data
     * @return $this
     */
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'filter_' . mb_strtolower($key);
            if (method_exists($this, $method)) $value = $this->$method($value);
            if ($value === '') $value = null;
            $this->$key = $value;
        }
        return $this;
    }
}
