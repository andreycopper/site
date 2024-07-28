<?php
namespace Controllers;

use System\Request;
use Entity\User\Event;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\MailException;
use Exceptions\UserException;
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
     * @throws UserException|ReflectionException|MailException|ForbiddenException
     */
    protected function actionReg(): void
    {
        if (Request::isPost()) {
            ValidationForm::isValidRegisterForm(Request::post());
            (new SystemRegister(Request::post('email'), Request::post('password')))->register();
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
            $code = Request::get('code');
            $event = Event::factory(['code' => $code, 'template' => ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, 'active' => false]);
            ValidationEvent::event($event);
            ValidationUser::isValidNotActiveUser($event->getUser());
            (new SystemRegister($event->getUser()->getEmail(), $event->getUser()->getPassword()))->confirm($event);
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
