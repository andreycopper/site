<?php
namespace Controllers;

use Views\View;
use System\Request;
use System\Response;

/**
 * Class Errors
 * @package App\Controllers
 */
class Errors extends Controller
{
    private int $code;
    private string $message;

    public function __construct($e)
    {
        $this->code = $e->getCode();
        $this->message = $e->getMessage();

        $this->view = new View();
        $this->set('code', $this->code);
        $this->set('message', $this->message);
    }

    /**
     * Show error page
     * @return void
     */
    protected function actionError(): void
    {
        if (Request::isAjax()) Response::result($this->code, false, $this->message);

        $status = match ($this->code) {
            200 => 'HTTP/1.1 200 OK',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            default => 'HTTP/1.1 500 Internal Server Error',
        };

        header($status, $this->code ?: 500);
        $this->display('error');
        die();
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): Errors
    {
        $this->code = $code;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Errors
    {
        $this->message = $message;
        return $this;
    }
}
