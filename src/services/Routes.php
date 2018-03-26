<?php


namespace App\services;

use Symfony\Component\HttpFoundation\Request;
use FastRoute\RouteCollector as Collector;
use Symfony\Component\Yaml\Yaml;

class Routes extends AppFactory
{
    private $routes;
    private $prefix;
    private $config;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->prefix = $this->config->getPrefix();
        $this->setRoutes();
    }

    private function setRoutes()
    {
        $yaml = new Yaml();
        $this->routes = $yaml->parseFile(__DIR__.'/../../config/routes.yaml');
        $this->dispatcher();
    }

    private function dispatcher()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (Collector $routes) {
            foreach ($this->routes as $value) {
                $routes->addRoute(
                    $value['method'],
                    $this->prefix.$value['url'],
                    ['controller' => $value['controller'],
                        'action' => $value['action'], 'bundle' => $value['bundle'] ]
                );
            }
        });


        $http = new Request();
        $request = $http->createFromGlobals();

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getBasePath().$request->getPathInfo());

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $controller = "ErrorController";
                $action = "notFound";
                $bundle = "error";
                $vars = "";

                $this->initController($routeInfo, $bundle, $controller, $action);
                break;

            case \FastRoute\Dispatcher::FOUND:
                $controller = ucwords($routeInfo[1]['controller']);
                $action = $routeInfo[1]['action'];
                $bundle = $routeInfo[1]['bundle'];
                $vars = $routeInfo[2];

                $this->initController($routeInfo, $bundle, $controller, $action);

                break;
        }
    }

    public function initController($routeInfo, $bundle, $controller, $action)
    {
        $container = new Container();
        $cont = $container->createConfig($routeInfo);
        $class = "App\\controller\\".$bundle."\\".$controller;

        $cont->call([$class,$action]);
    }
}
