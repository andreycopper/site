<?php

namespace System;

class Request
{
    public static function request($id = null)
    {
        if (empty($id)) {
            if (!empty($_REQUEST) && is_array($_REQUEST)) {
                $res = [];

                foreach ($_REQUEST as $key => $value) {
                    $res[$key] = $value;
                }
            }
            return $res ?? null;
        }
        else {
            return $_REQUEST[$id] ?? null;
        }
    }

    public static function get($id = null)
    {
        if (empty($id)) {
            if (!empty($_GET) && is_array($_GET)) {
                $res = [];

                foreach ($_GET as $key => $value) {
                    $res[$key] = $value;
                }
            }
            return $res ?? null;
        }
        else {
            return $_GET[$id] ?? null;
        }
    }

    public static function post($id = null)
    {
        if (empty($id)) {
            if (!empty($_POST) && is_array($_POST)) {
                $res = [];

                foreach ($_POST as $key => $value) {
                    $res[$key] = $value;
                }
            }
            return $res ?? null;
        }
        else {
            return $_POST[$id] ?? null;
        }
    }

    public static function isGet()
    {
        return 'GET' === $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost()
    {
        return 'POST' === $_SERVER['REQUEST_METHOD'];
    }

    public static function isPut()
    {
        return 'PUT' === $_SERVER['REQUEST_METHOD'];
    }

    public static function isDelete()
    {
        return 'DELETE' === $_SERVER['REQUEST_METHOD'];
    }

    public static function isPatch()
    {
        return 'PATCH' === $_SERVER['REQUEST_METHOD'];
    }

    public static function isAjax()
    {
        return
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'];
    }
}
