<?php
namespace System;

class Response
{
    /**
     * Возвращает ответ пользователю
     * @param int $code
     * @param bool $result - результат работы
     * @param string $message - сообщение
     * @param array|object $data - данные
     * @return bool|void
     */
    public static function result(int $code = 200, bool $result = true, string $message = '', array|object $data = [])
    {
        $status = match ($code) {
            200 => 'HTTP/1.1 200 OK',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            default => 'HTTP/1.1 500 Internal Server Error',
        };

        http_response_code($code);
        header($status);

        if (Request::isAjax()) {
            echo json_encode(['result' => $result, 'message' => $message, 'data' => $data]);
            die;
        }
        else return $result;
    }

    /**
     * Возвращает ответ пользователю !!!api
     * @param bool $result - результат работы
     * @param string $message - сообщение
     * @param $data - данные
     */
    public static function apiResult(int $code, bool $result, string $message = '', $data = [], $token = null)
    {
        $status = match ($code) {
            200 => 'HTTP/1.1 200 OK',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            default => 'HTTP/1.1 400 Bad Request',
        };

        http_response_code($code);
        header($status);

        echo json_encode([
            'result' => $result,
            'message' => $message,
            'data' => $data,
            'token' => $token,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        die;
    }
}
