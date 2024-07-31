<?php
namespace Controllers;

use System\Request;
use Entity\User\Event;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\MailException;
use Exceptions\UserException;
use System\Response;
use Utils\Data\ValidationForm;
use Utils\Data\ValidationUser;
use Utils\Data\ValidationEvent;
use Exceptions\ForbiddenException;
use System\Register as SystemRegister;
use Models\User\Event as ModelUserEvent;

/**
 * Class Register
 * @package Controllers
 */
class Register extends Controller
{
    /**
     * Register form
     * @return void
     */
    protected function actionDefault(): void
    {
        $this->display('register');
    }

    /**
     * User register
     * @return void
     * @throws UserException|ReflectionException|MailException|ForbiddenException|DbException
     */
    protected function actionReg(): void
    {
        if (Request::isPost()) {
            ValidationForm::isValidRegisterForm(Request::post());

            $email = (new SystemRegister())->register(Request::post('email'), Request::post('password'));

            if (Request::isAjax()) Response::result(200, true, $email);
            else {
                header("Location: /register/success/{$email}");
                die;
            }
        }
    }

    /**
     * Success register page
     * @param string|null $email - login
     * @return void
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    protected function actionSuccess(?string $email = null): void
    {
        ValidationUser::isExistNotActiveUserEmail($email);
        $this->set('email', $email);
        $this->display('register_success');
    }

    /**
     * Email confirm
     * @return void
     * @throws DbException|UserException|MailException|ReflectionException|ForbiddenException
     */
    protected function actionConfirm(): void
    {
        $code = Request::get('code');

        if (!empty($code)) {
            $email = (new SystemRegister(ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, $code))->confirm();

            if (Request::isAjax()) Response::result(200, true, $email);
            else {
                header("Location: /register/finish/{$email}/");
                die;
            }
        }

        $this->set('code', $code);
        $this->display('register_confirm');
    }

    /**
     * Success confirm page
     * @param string $email - login
     * @return void
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    protected function actionFinish(string $email): void
    {
        ValidationUser::isExistActiveUserEmail($email);
        $this->set('email', $email);
        $this->display('register_finish');
    }
}
