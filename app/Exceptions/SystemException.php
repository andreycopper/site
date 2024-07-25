<?php
namespace Exceptions;

use Throwable;
use System\Loggers\SystemLogger;

/**
 * Class UserException
 * @package App\Exceptions
 */
class SystemException extends BaseException
{
    public function __construct($message = 'System error', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        SystemLogger::getInstance()->error($this);
    }
}
