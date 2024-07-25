<?php
namespace Exceptions;

use Throwable;
use System\Loggers\ErrorLogger;

/**
 * Class UserException
 * @package App\Exceptions
 */
class CryptException extends BaseException
{
    public function __construct($message = 'Crypt error', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        ErrorLogger::getInstance()->error($this);
    }
}
