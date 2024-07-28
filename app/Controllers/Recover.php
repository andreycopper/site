<?php
namespace Controllers;

use System\Request;
use System\Response;
use Entity\User\Event;
use ReflectionException;
use Exceptions\DbException;
use Exceptions\MailException;
use Exceptions\UserException;
use Utils\Data\ValidationUser;
use Utils\Data\ValidationEvent;
use Utils\Data\ValidationForm;
use Exceptions\ForbiddenException;
use System\Recovery as SystemRecovery;
use Models\User\Event as ModelUserEvent;

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
     * @throws UserException|MailException|ReflectionException|ForbiddenException|DbException
     */
    protected function actionSubmit(): void
    {
        if (Request::isPost()) {
            ValidationForm::isValidRecoveryEmailForm(Request::post());

            $event = Event::factory(['email' => Request::post('email'), 'template' => ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY]);
            ValidationEvent::isEventNotExist($event);

            (new SystemRecovery(Request::post('email')))->submit();
        }
    }

    /**
     * Success recovery page
     * @param string|null $email - email
     * @return void
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    protected function actionSuccess(?string $email = null): void
    {
        ValidationUser::isExistActiveUserEmail($email);
        $this->set('email', $email);
        $this->display('recover_email');
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
            $event = Event::factory(['code' => $code, 'template' => ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY, 'active' => false]);
            ValidationEvent::event($event);
            ValidationUser::isValidActiveUser($event->getUser());

            if (Request::isPost()) {
                ValidationForm::isValidRecoveryPasswordForm(Request::post());
                (new SystemRecovery($event->getUser()->getEmail()))->setUser($event->getUser())->setEvent($event)->recover();
            }

            if (Request::isAjax()) Response::result(200, true, $code);
            else $this->display('recover_password');
        }
    }

    /**
     * Success confirm page
     * @param string $email - email
     * @return void
     * @throws DbException|ForbiddenException|ReflectionException|UserException
     */
    protected function actionFinish(string $email): void
    {
        ValidationUser::isExistActiveUserEmail($email);
        $this->set('email', $email);
        $this->display('recover_finish');
    }
}
