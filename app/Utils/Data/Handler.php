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
     * Clear user login
     * @param $value - value
     * @return string
     */
    public static function toLogin($value): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', trim($value));
    }

    /**
     * Clear user email
     * @param $value - value
     * @return string
     */
    public static function toEmail($value): string
    {
        return preg_replace('/[^A-Za-z0-9@_.-]/', '', trim($value));
    }

    /**
     * Clear user password
     * @param $value - value
     * @return string
     */
    public static function toPassword($value): string
    {
        return preg_replace('/[^A-Za-z0-9!@#$%^&*_.-]/', '', trim($value));
    }
}
