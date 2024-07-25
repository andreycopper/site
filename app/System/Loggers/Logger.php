<?php
namespace System\Loggers;

use Traits\Singleton;
use Psr\Log\AbstractLogger;

/**
 * Class Logger
 * @package App\System
 */
abstract class Logger extends AbstractLogger
{
    protected  $resource;

    use Singleton;

    protected function __construct()
    {
        $logger = match (true) {
            $this instanceof AccessLogger => 'access',
            $this instanceof SystemLogger => 'system',
            $this instanceof WarningLogger => 'warning',
            default => 'error',
        };

        $yearDir = DIR_LOGS . DIRECTORY_SEPARATOR . date('Y');
        $monthDir = $yearDir . DIRECTORY_SEPARATOR . date('m');
        $dateDir = $monthDir . DIRECTORY_SEPARATOR . date('d');

        if (!is_dir($yearDir)) mkdir($yearDir);
        if (!is_dir($monthDir)) mkdir($monthDir);
        if (!is_dir($dateDir)) mkdir($dateDir);

        $this->resource = fopen($dateDir . DIRECTORY_SEPARATOR . "{$logger}.log", 'a');
    }

    /**
     * Формирует строку с описанием пойманного исключения и записывает ее в лог-файл
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $level = ucfirst($level);

        $get = json_encode($_GET);
        $post = json_encode(!empty($_POST['password']) ? array_diff_assoc($_POST, ['password' => $_POST['password']]) : $_POST);
        $files = json_encode($_FILES);

        $log = "[{$date}]\n{$level}: $message\nIP: {$_SERVER['REMOTE_ADDR']}\nGET: {$get}\nPOST: {$post}\nFILES: {$files}\n";

        if (!empty($context) && is_array($context)) {
            foreach ($context as $item) {
                $log .= $item . "\n";
            }
        }

        $log .= "==================================================\n";
        fwrite($this->resource, $log);
    }
}
