<?php
namespace Traits;

/*
 * trait Singleton
 * Реализует паттерн Singleton
 * @package App\Traits
 */
trait Singleton
{
    private static $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (static::$instance === null) static::$instance = new static();
        return static::$instance;
    }
}
