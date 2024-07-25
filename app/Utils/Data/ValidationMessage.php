<?php
namespace Utils\Data;

class ValidationMessage extends Validation
{
    /**
     * Checking message exist in request
     * @param ?string $message - message
     * @return bool
     */
    public static function message(?string $message = null): bool
    {
        return !empty($message);
    }
}
