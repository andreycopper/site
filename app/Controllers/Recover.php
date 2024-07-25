<?php
namespace Controllers;

use Entity;
use System\Request;
use Entity\User\Event;
use Utils\Data;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\MailException;
use Exceptions\UserException;
use Exceptions\NotFoundException;
use Exceptions\ForbiddenException;
use System\Recovery as SystemRecovery;
use Utils\Data\ValidationUser;
use Utils\Data\ValidationEvent;
use Utils\Data\ValidationForm;

/**
 * Class Recover
 * @package Controllers
 */
class Recover extends Controller
{
    /**
     * Recover form
     * @return void
     */
    protected function actionDefault(): void
    {
        $this->display('recover');
    }

    /**
     * Send recovery code
     * @return void
     * @throws UserException|MailException|ReflectionException|ForbiddenException
     */
    protected function actionSubmit(): void
    {
        if (Request::isPost()) {
            ValidationForm::isValidRecoveryEmailForm(Request::post());
            (new SystemRecovery(Request::post('email')))->submit();
        }
    }

    /**
     * Change password
     * @return void
     * @throws UserException|DbException|ReflectionException|MailException|ForbiddenException
     */
    protected function actionPassword(): void
    {
        $code = Request::get('code');
        $this->set('code', $code);

        if (empty($code)) $this->display('recover_code');
        else {
            $event = Event::factory(['code' => $code, 'active' => false]);
            ValidationEvent::event($event);
            ValidationUser::isValidActiveUser($event->getUser());

            if (Request::isPost()) {
                ValidationForm::isValidRecoveryPasswordForm(Request::post());
                (new SystemRecovery())->setUser($event->getUser())->setEvent($event)->recover();
            }

            $this->display('recover_password');
        }
    }

    /**
     * Success recovery page
     * @param $login - login
     * @return void
     * @throws NotFoundException
     */
    protected function actionSuccess(?string $login = null): void
    {
        if (empty($login)) throw new NotFoundException();

        $this->set('login', $login);
        $this->display('recover_email');
    }

    /**
     * Success confirm page
     * @param string $login - login
     * @return void
     */
    protected function actionFinish(string $login): void
    {
        $this->set('login', $login);
        $this->display('recover_finish');
    }
}
