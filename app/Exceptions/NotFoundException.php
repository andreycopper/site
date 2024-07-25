<?php
namespace Exceptions;

use Throwable;
use System\Loggers\ErrorLogger;

/**
 * Class NotFoundException
 * @package App\Exceptions
 */
class NotFoundException extends BaseException
{
    public function __construct($message = 'Page not found', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        ErrorLogger::getInstance()->error($this);
    }
}
