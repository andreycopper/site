<?php
namespace Controllers;

use Utils\Csrf;
use Views\View;
use Entity\User;
use Models\Model;
use System\Crypt;
use System\Request;
use System\Response;
use ReflectionException;
use Models\User as ModelUser;
use System\Auth as SystemAuth;
use Exceptions\NotFoundException;
use Exceptions\ForbiddenException;

/**
 * Class Controller
 * @package App\Controllers
 */
abstract class Controller
{
    protected View $view;
    protected ?User $user;
    protected ?Crypt $crypt;
    protected string $csrf;
    protected ?Model $model = null;

    /**
     * Controller constructor
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->view = new View();
        $this->user = ModelUser::getCurrent();
        $this->csrf = Csrf::get();
        $this->crypt = ModelUser::isAuthorized() ? new Crypt($this->user->getPublicKey(), $this->user->getPrivateKey()) : null;

        $this->set('user', $this->user);
        $this->set('csrf', $this->csrf);
        $this->set('crypt', $this->crypt);
    }

    /**
     * Call the class & method
     * @param string $action - method
     * @param array $params - params
     * @return void
     * @throws ForbiddenException|NotFoundException|ReflectionException
     */
    public function action(string $action, array $params = []): void
    {
        if (method_exists($this, $action)) {
            if ($this->access($this, $action)) {
                if (method_exists($this, 'before')) $this->before();

                if (count($params) === 3)
                    $this->$action(mb_strtolower($params[0]), mb_strtolower($params[1]), mb_strtolower($params[2]));
                elseif (count($params) === 2)
                    $this->$action(mb_strtolower($params[0]), mb_strtolower($params[1]));
                elseif (count($params) === 1)
                    $this->$action(mb_strtolower($params[0]));
                else
                    $this->$action();

                if (method_exists($this, 'after')) $this->after();
                die;
            } else throw new ForbiddenException();
        } else throw new NotFoundException();
    }

    /**
     * Check access for $this class & method
     * @param $object - context
     * @param $action - called method
     * @return bool
     * @throws ReflectionException
     */
    protected function access($object, $action): bool
    {
        $class = get_class($object);

        if (in_array($class, ['Controllers\Auth', 'Controllers\Register', 'Controllers\Recover'])) {
            if (ModelUser::isAuthorized()) {
                header('Location: /');
                die;
            }
        } elseif ($class !== 'Controllers\Errors') {
            if (!ModelUser::isAuthorized()) {
                SystemAuth::logout();

                if (Request::isAjax()) Response::result(403, false, 'Forbidden');
                else {
                    header('HTTP/1.1 403 Forbidden', true, 403);
                    header('Location: /auth/');
                    die;
                }
            }
        }

        return true;
    }

    /**
     * Make var for View
     * @param $var - variable
     * @param $value - variable value
     * @return void
     */
    protected function set($var, $value = null): void
    {
        $this->view->$var = $value;
    }

    /**
     * Set template directory
     * @param $template - template directory
     * @return void
     */
    protected function setTemplate(string $template): void
    {
        $this->view->setTemplate($template);
    }

    /**
     * Rendering file
     * @param string $file - file
     * @param array $vars - vars
     * @return string
     */
    protected function render(string $file, array $vars = []): string
    {
        return $this->view->render($file, $vars);
    }

    /**
     * View template
     * @param $file - file
     * @return void
     */
    protected function display($file): void
    {
        $this->view->display($file);
    }

    /**
     * View element
     * @param $file - file
     * @return void
     */
    protected function display_element($file): void
    {
        $this->view->display_element($file);
    }
}
