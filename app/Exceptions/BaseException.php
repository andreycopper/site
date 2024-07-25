<?php
namespace Exceptions;

use Throwable;

/**
 * Class BaseException
 * @package App\Exceptions
 */
class BaseException extends \Exception
{
    protected $code;
    protected $message;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->code = $code;
        $this->message = $message;
    }
}
