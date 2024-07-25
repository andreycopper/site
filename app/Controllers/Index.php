<?php
namespace Controllers;

use ReflectionException;
use System\Auth as SystemAuth;

/**
 * Class Index
 * @package Controllers
 */
class Index extends Controller
{
    protected function before(): void
    {
    }

    /**
     * Start page
     * @return void
     */
    protected function actionDefault(): void
    {
        $this->view->display('index');
    }

    /**
     * Logout
     * @return void
     * @throws ReflectionException
     */
    protected function actionLogout(): void
    {
        SystemAuth::logout();
        $this->display('logout');
    }
}
