<?php
namespace Exceptions;

use Throwable;
use System\Loggers\ErrorLogger;

/**
 * Class MailException
 * @package App\Exceptions
 */
class MailException extends BaseException
{
    public function __construct($message = 'Mailing error', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        ErrorLogger::getInstance()->error($this);
    }
}
