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
            $code = Request::get('code');
            $event = Event::factory(['code' => $code, 'active' => false, 'template' => ModelUserEvent::TEMPLATE_INVITATION]);
            ValidationEvent::event($event);
            ValidationForm::isValidRegisterForm(Request::post());
            (new SystemRegister(Request::post('login'), Request::post('password'), Request::post('email')))->register($event);
        }
    }

    /**
     * Success register page
     * @param string|null $login - login
     * @return void
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    protected function actionSuccess(?string $login = null): void
    {
        ValidationUser::isExistNotActiveUserLogin($login);
        $this->set('login', $login);
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
            $event = Event::factory(['code' => $code, 'active' => false]);
            ValidationEvent::event($event);
            ValidationUser::isValidUser($event->getUser());
            (new SystemRegister($event->getUser()->getLogin(), $event->getUser()->getPassword(), $event->getUser()->getEmail()))->confirm($event);
        }

        $this->set('code', $code);
        $this->display('register_confirm');
    }

    /**
     * Success confirm page
     * @param string $login - login
     * @return void
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    protected function actionFinish(string $login): void
    {
        ValidationUser::isExistActiveUserLogin($login);
        $this->set('login', $login);
        $this->display('register_finish');
    }
}
