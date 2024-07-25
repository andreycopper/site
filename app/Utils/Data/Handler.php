<?php
namespace Utils\Data;

class Handler
{
    /**
     * Convert to int
     * @param $value - value
     * @return int
     */
    public static function toInt($value): int
    {
        return intval(preg_replace('/[^0-9]/', '', trim($value)));
    }

    /**
     * Clear user request
     * @param $value - value
     * @return string
     */
    public static function toLogin($value): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', trim($value));
    }
}
