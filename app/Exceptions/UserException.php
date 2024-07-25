<?php
namespace Exceptions;

use Throwable;
use System\Loggers\ErrorLogger;

/**
 * Class UserException
 * @package App\Exceptions
 */
class UserException extends BaseException
{
    public function __construct($message = 'User error', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        ErrorLogger::getInstance()->error($this);
    }
}
