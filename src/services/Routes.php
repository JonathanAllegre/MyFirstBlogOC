<?php


namespace App\services;

use Symfony\Component\HttpFoundation\Request;
use FastRoute\RouteCollector as Collector;
use Symfony\Component\Yaml\Yaml;

class Routes
{
    private $routes;
    private $prefix;

    public function __construct(Config $config)
    {
        $this->prefix = $config->getPrefix();
        $this->setRoutes();
    }

    private function setRoutes()
    {
        $this->routes = Yaml::parseFile(__DIR__.'/../../config/Routes.yaml');
        $this->dispatcher();
    }

    private function dispatcher()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (Collector $routes) {
            foreach ($this->routes as $value) {
                if ($value['url'] == "/") {
                    $value['url'] = "";
                }
                if ($value['url'] !== "/" && $this->prefix == "/") {
                    $value['url'] = substr($value['url'], 1);
                }
                $routes->addRoute(
                    $value['method'],
                    $this->prefix.$value['url'],
                    ['controller' => $value['controller'],
                        'action' => $value['action'], 'bundle' => $value['bundle'] ]
                );
            }
        });


        $request = Request::createFromGlobals();

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getBasePath().$request->getPathInfo());


        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $controller = "ErrorController";
                $action = "notFound";
                $bundle = "error";
                $vars = "";

                $this->initController($bundle, $controller, $action, $vars);
                break;

            case \FastRoute\Dispatcher::FOUND:
                $controller = ucwords($routeInfo[1]['controller']);
                $action = $routeInfo[1]['action'];
                $bundle = $routeInfo[1]['bundle'];
                $vars = $routeInfo[2];

                $this->initController($bundle, $controller, $action, $vars);

                break;
        }
    }

    public function initController($bundle, $controller, $action, $vars)
    {
        $class = "App\\controller\\".$bundle."\\".$controller;
        $cont = new $class;
        $cont->$action($vars);
    }
}
