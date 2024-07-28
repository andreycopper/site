<?php
namespace Entity;

use DateTime;
use Traits\Magic;
use ReflectionClass;
use ReflectionException;

abstract class Entity
{
    use Magic;

    /**
     * List entity object from model list
     * @param array $params - params
     * @return array
     * @throws ReflectionException
     */
    public static function getList(array $params = []): array
    {
        $items = static::MODEL::getList($params);
        $list = [];

        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                $list[] = (new static())->init($item);
            }
        }

        return $list;
    }

    /**
     * Entity static object from model
     * @param array $params - params
     * @return ?static
     * @throws ReflectionException
     */
    public static function factory(array $params): ?static
    {
        $model = static::get($params);

        if (empty($model)) return null;
        $object = new static();
        $object->init($model, $params);
        return $object;
    }

    /**
     * Get model by
     * @param ?array $params - params
     * @return ?array
     */
    public static function get(?array $params): ?array
    {
        return match (true) {
            !empty($params['id']) => static::MODEL::getById($params['id'], $params),
            !empty($params['login']) => static::MODEL::getByLogin($params['login'], $params),
            !empty($params['email']) => static::MODEL::getByEmail($params['email'], $params),
            !empty($params['token']) => static::MODEL::getByToken($params['token'], $params),
            !empty($params['cookie']) => static::MODEL::getByCookie($params['cookie'], $params),
            !empty($params['user_id']) => static::MODEL::getByUserId($params['user_id'], $params),
            !empty($params['code']) => static::MODEL::getByCode($params['code'], $params),
            !empty($params['ip']) => static::MODEL::getByIp($params['ip'], $params),
            !empty($params['user_id_out']) && !empty($params['user_id_in']) => static::MODEL::getByUsersId($params['user_id_out'], $params['user_id_in'], $params),
            default => null,
        };
    }

    /**
     * Convert model to entity
     * @param ?array $data
     * @param ?array $params
     * @return ?static
     * @throws ReflectionException
     */
    public function init(?array $data, ?array $params = []): ?static
    {
        if (empty($data)) return null;

        $reflectionClass = new ReflectionClass(static::class);
        $fields = $this->getFields();
        foreach ($fields as $key => $field) {
            $prop = $field['field'];
            $type = $field['type'];

            if ($reflectionClass->hasProperty($prop)) {
                $reflectionProperty = $reflectionClass->getProperty($prop);
                $reflectionProperty->setAccessible(true);

                if (!array_key_exists($key, $data)) {
                    $reflectionProperty->setValue($this, null);
                }
                else $reflectionProperty->setValue($this, $this->convertValue($type, $data[$key]));
            }
        }

        return $this;
    }

    /**
     * Convert value
     * @param $type - var type
     * @param $value - value
     * @return mixed
     * @throws ReflectionException
     */
    private function convertValue($type, $value): mixed
    {
        switch ($type) {
            case 'int':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'bool':
                return (bool)$value;
            case 'datetime':
                return !empty($value) ?
                    ($value instanceof DateTime ?
                        $value :
                        (is_string($value) ?
                            DateTime::createFromFormat('Y-m-d H:i:s', $value) :
                            (is_array($value) ? DateTime::__set_state($value) : null))) :
                    null;
            case 'array':
                return json_decode($value, true);
            default:
                if (class_exists("Entity\\$type")) {
                    if ($type === 'User' && !empty($_SESSION['user']) && !empty($_SESSION['user']->getId()) && $_SESSION['user']->getId() === $value) {
                        $value = $_SESSION['user'];
                    }
                    else {
                        $reflectionClass = new ReflectionClass("Entity\\$type");
                        if ($reflectionClass->isSubclassOf('Entity\Entity')) {
                            $value = $reflectionClass->getMethod('factory')->invoke(null, ['id' => $value, 'active' => false]);
                        }
                    }
                }

                return $value;
        }
    }

    public function save(): bool|int
    {
        return (new (static::MODEL))->init($this)->save();
    }

    public function delete(): bool|int
    {
        return (new (static::MODEL))->init($this)->delete();
    }
}
