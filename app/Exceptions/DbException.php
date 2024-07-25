<?php
namespace Exceptions;

use Throwable;
use System\Loggers\ErrorLogger;

/**
 * Class DbException
 * @package App\Exceptions
 */
class DbException extends BaseException
{
    public function __construct($message = 'Something went wrong. Come in later.', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        ErrorLogger::getInstance()->error($this);
    }
}
