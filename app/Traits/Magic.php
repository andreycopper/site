<?php

namespace Traits;

/**
 * Реализует магические методы __set(), __get() и __isset()
 * Trait Magic
 * @package App\Traits
 */
trait Magic
{
    private array $data = [];

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function &__get($name)
    {
        return $this->data[$name];
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}
