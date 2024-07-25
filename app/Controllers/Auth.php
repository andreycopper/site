<?php
namespace Controllers;

use System\Request;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\UserException;
use Utils\Data\ValidationForm;
use System\Auth as SystemAuth;
use Exceptions\ForbiddenException;

/**
 * Class Auth
 * @package Controllers
 */
class Auth extends Controller
{
    /**
     * Auth form
     */
    protected function actionDefault(): void
    {
        $this->display('auth');
    }

    /**
     * User auth
     * @return void
     * @throws UserException|DbException|ReflectionException|ForbiddenException
     */
    protected function actionLogin(): void
    {
        if (Request::isPost()) {
            ValidationForm::isValidAuthForm(Request::post());
            (new SystemAuth(Request::post('email'), Request::post('password')))->auth();
        }
    }
}
