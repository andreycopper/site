<?php
require __DIR__ . '/../config/autoload.php';
require __DIR__ . '/../vendor/autoload.php';
session_start();
require __DIR__ . '/../config/constants.php';
require __DIR__ . '/../app/System/ErrorSupervisor.php';

use System\Route;
use Entity\IpBlock;
use System\Security;
use Controllers\Errors;
use Utils\Data\Validation;
use System\ErrorSupervisor;
use Exceptions\SystemException;
use System\Loggers\SystemLogger;
use Exceptions\ForbiddenException;

new ErrorSupervisor();
Security::array_xss_clean($_GET);
Security::array_xss_clean($_POST);

try {
    $ipBlock = IpBlock::factory(['ip' => $_SERVER['REMOTE_ADDR']]);
    if (!empty($ipBlock) && !empty($ipBlock->getId())) {
        $dt = "{$ipBlock->getExpire()->format('d.m.Y H:i')} {$ipBlock->getExpire()->getTimezone()->getName()}";
        throw new ForbiddenException(Validation::USER_IP_TEMPORARILY_BLOCKED_TILL . $dt . '<br>' . Validation::REASON . ": {$ipBlock->getReason()}", 403);
    }

    (new Route($_SERVER['REQUEST_URI']))->start();
} catch (Exception $e) {
    (new Errors($e))->action('actionError');
} catch(TypeError $e) {
    SystemLogger::getInstance()->error($e);
    (new Errors(new SystemException()))->action('actionError');
}
