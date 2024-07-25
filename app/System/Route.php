<?php
namespace System;

use ReflectionClass;
use ReflectionException;
use Exceptions\NotFoundException;

/**
 * Class Route
 * @package App\System
 */
class Route
{
    private ?array $routes = [];

    private ?string $controller = null;
    private ?string $action = null;
    private ?array $params = [];

    public function __construct(string $uri)
    {
        $uri = explode('#', $uri)[0];
        $uri = explode('?', $uri)[0];
        $uri = trim($uri, '/');

        if (!empty($uri)) {
            $parts  = explode('/', $uri);

            foreach ($parts as $part)
                $this->routes[] = ucfirst(str_replace('-', '_', $part));
        }

        define('ROUTE', array_values($this->routes)); // ['Catalog', 'Conditioners', 'Mobile', '335']
    }

    /**
     * Формируется адрес контроллера и его экшн по типу \App\Controller\Catalog -> actionDefault
     * Сначала проверяется путь App\Controller\Blog\News\10 -> actionShow(10)
     * Затем проверяется путь App\Controller\Blog\News\Edit\10 -> actionEdit(10)
     * Затем проверяется путь App\Controller\Blog\News -> actionDefault()
     * @throws NotFoundException|ReflectionException
     */
    public function start(): void
    {
        if (!empty($this->routes[0]) && in_array($this->routes[0], ['Js', 'Css'])) return;

        $count  = count($this->routes);
        while ($count >= 0) {
            $firstArray = array_slice($this->routes, 0, $count);
            $secondArray = array_slice($this->routes, $count);

            if ($this->getUserClassUserMethod($firstArray, $secondArray) || $this->getUserClassShowMethod($firstArray, $secondArray) ||
                $this->getUserClassDefaultMethod($firstArray, $secondArray) || $this->getIndexClassUserMethod($firstArray, $secondArray) ||
                $this->getIndexClassShowMethod($firstArray, $secondArray) || $this->getIndexClassDefaultMethod($firstArray, $secondArray)) break;

            $count--;
        }

        if (!empty($this->controller) && !empty($this->action)) $this->run();
        else throw new NotFoundException();
    }

    /**
     * Run controller -> method
     * @return void
     * @throws NotFoundException
     */
    public function run(): void
    {
        if (!empty($this->controller) && !empty($this->action)) {
            $controller = new $this->controller;
            $controller->action($this->action, $this->params);
        }
        else throw new NotFoundException();
    }

    /**
     * Makes controller & method from user data
     * @param array $firstArray - controller array
     * @param array $secondArray - method & params array
     * @return bool
     * @throws ReflectionException
     */
    private function getUserClassUserMethod(array $firstArray, array $secondArray): bool
    {
        if (!empty($firstArray) && !empty($secondArray)) {
            $class = 'Controllers\\' . implode('\\', $firstArray);
            $method = "action{$secondArray[0]}";
            $params = array_slice($secondArray, 1);

            if (class_exists($class) && method_exists($class, $method)) {
                $reflectionMethod = (new ReflectionClass($class))->getMethod($method);

                if (count($params) <= $reflectionMethod->getNumberOfParameters() && count($params) >= $reflectionMethod->getNumberOfRequiredParameters()) {
                    $this->controller = $class;
                    $this->action = $method;
                    $this->params = $params;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Makes controller & show method from user data
     * @param array $firstArray - controller array
     * @param array $secondArray - method & params array
     * @return bool
     * @throws ReflectionException
     */
    private function getUserClassShowMethod(array $firstArray, array $secondArray): bool
    {
        if (!empty($firstArray) && !empty($secondArray) && count($secondArray) === 1) {
            $class = 'Controllers\\' . implode('\\', $firstArray);
            $method = "actionShow";
            $params = $secondArray;

            if (class_exists($class) && method_exists($class, $method)) {
                $reflectionMethod = (new ReflectionClass($class))->getMethod($method);

                if (count($params) === $reflectionMethod->getNumberOfRequiredParameters()) {
                    $this->controller = $class;
                    $this->action = $method;
                    $this->params = $params;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Makes controller & default method from user data
     * @param array $firstArray - controller array
     * @param array $secondArray - method & params array
     * @return bool
     * @throws ReflectionException
     */
    private function getUserClassDefaultMethod(array $firstArray, array $secondArray): bool
    {
        if (!empty($firstArray)) {
            $class = 'Controllers\\' . implode('\\', $firstArray);
            $method = "actionDefault";
            $params = $secondArray;

            if (class_exists($class) && method_exists($class, $method)) {
                $reflectionMethod = (new ReflectionClass($class))->getMethod($method);

                if (count($params) <= $reflectionMethod->getNumberOfParameters() && count($params) >= $reflectionMethod->getNumberOfRequiredParameters()) {
                    $this->controller = $class;
                    $this->action = $method;
                    $this->params = $params;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Makes index controller & method from user data
     * @param array $firstArray - controller array
     * @param array $secondArray - method & params array
     * @return bool
     * @throws ReflectionException
     */
    private function getIndexClassUserMethod(array $firstArray, array $secondArray): bool
    {
        $indexArray = array_merge($firstArray, ['Index']);
        if (!empty($secondArray)) {
            $class = 'Controllers\\' . implode('\\', $indexArray);
            $method = "action{$secondArray[0]}";
            $params = array_slice($secondArray, 1);

            if (class_exists($class) && method_exists($class, $method)) {
                $reflectionMethod = (new ReflectionClass($class))->getMethod($method);

                if (count($params) <= $reflectionMethod->getNumberOfParameters() && count($params) >= $reflectionMethod->getNumberOfRequiredParameters()) {
                    $this->controller = $class;
                    $this->action = $method;
                    $this->params = $params;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Makes index controller & show method from user data
     * @param array $firstArray - controller array
     * @param array $secondArray - method & params array
     * @return bool
     * @throws ReflectionException
     */
    private function getIndexClassShowMethod(array $firstArray, array $secondArray): bool
    {
        $indexArray = array_merge($firstArray, ['Index']);
        if (!empty($secondArray) && count($secondArray) === 1) {
            $class = 'Controllers\\' . implode('\\', $indexArray);
            $method = "actionShow";
            $params = $secondArray;

            if (class_exists($class) && method_exists($class, $method)) {
                $reflectionMethod = (new ReflectionClass($class))->getMethod($method);

                if (count($params) === $reflectionMethod->getNumberOfRequiredParameters()) {
                    $this->controller = $class;
                    $this->action = $method;
                    $this->params = $params;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Makes index controller & default method from user data
     * @param array $firstArray - controller array
     * @param array $secondArray - method & params array
     * @return bool
     * @throws ReflectionException
     */
    private function getIndexClassDefaultMethod(array $firstArray, array $secondArray): bool
    {
        $indexArray = array_merge($firstArray, ['Index']);
        $class = 'Controllers\\' . implode('\\', $indexArray);
        $method = "actionDefault";
        $params = $secondArray;

        if (class_exists($class) && method_exists($class, $method)) {
            $reflectionMethod = (new ReflectionClass($class))->getMethod($method);

            if (count($params) <= $reflectionMethod->getNumberOfParameters() && count($params) >= $reflectionMethod->getNumberOfRequiredParameters()) {
                $this->controller = $class;
                $this->action = $method;
                $this->params = $params;
                return true;
            }
        }

        return false;
    }
}
