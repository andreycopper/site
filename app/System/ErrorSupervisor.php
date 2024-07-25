<?php
namespace System;

use Throwable;
use Exceptions\SystemException;
use System\Loggers\ErrorLogger;
use System\Loggers\WarningLogger;

class ErrorSupervisor
{
    public function __construct()
    {
        set_error_handler([$this, 'OtherErrorCatcher']);
        register_shutdown_function([$this, 'FatalErrorCatcher']);
        set_exception_handler([$this, 'ExceptionCatcher']);
        ob_start();
    }

    /**
     * Обрабатывает не критические ошибки
     * @param $errno - код
     * @param $errstr - сообщение
     * @param null $errfile - файл
     * @param null $errline - строка
     * @return void
     */
    public function OtherErrorCatcher($errno, $errstr, $errfile = null, $errline = null): void
    {
        WarningLogger::getInstance()->warning("Lvl {$errno}. {$errstr}\n{$errfile}:{$errline}");
    }

    /**
     * Обрабатывает критические ошибки
     * @return void
     *@throws SystemException
     */
    public function FatalErrorCatcher(): void
    {
        if (!empty($error) && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR])) {
            ob_end_clean();
            throw new SystemException($error['message']);
        } else ob_end_flush();
    }

    /**
     * Обрабатывает неперхваченные исключения
     * @param Throwable $e
     * @return void
     */
    public function ExceptionCatcher(Throwable $e): void
    {
        ErrorLogger::getInstance()->error("Code {$e->getCode()}. {$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}");
        echo $e->getMessage();
        die;
    }
}
