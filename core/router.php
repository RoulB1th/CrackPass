<?php declare(strict_types=1);

namespace Core;

require_once ROOT . '/vendor/autoload.php';
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

class router
{
    public static $dispatcher;
    public static array $routes;

    public static function run()
    {
        static::$dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $router) {
            foreach (static::$routes as $route) {
                $router->addRoute(
                    $route['method'],
                    $route['path'],
                    $route['controller']
                );
            }
        });
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = static::$dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                echo "not found";
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                echo "not allowed buddy";
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                call_user_func_array($handler, $vars);
                break;
        }
    }
    public static function GET($uri, $handler): void
    {
        static::$routes[$uri] = [
            'method' => 'GET',
            'path' => $uri,
            'controller' => $handler
        ];
    }
    public static function POST($uri, $handler): void
    {
        static::$routes[$uri] = [
            'method' => 'POST',
            'path' => $uri,
            'controller' => $handler
        ];
    }

}