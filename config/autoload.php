<?php

spl_autoload_register(
    function ($class){
        $name = str_replace('\\', '/', $class);
        $appDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app';
        $file = $appDir . DIRECTORY_SEPARATOR . $name . '.php';
        if (file_exists($file)) require_once $file;
    }
);
