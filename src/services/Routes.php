<?php


namespace App\services;

use Symfony\Component\HttpFoundation\Request;
use FastRoute\RouteCollector as Collector;
use Symfony\Component\Yaml\Yaml;

class Routes
{
    private $routes;

    public function __construct()
    {
        $this->routes = Yaml::parseFile(__DIR__.'/../../config/Routes.yaml');
        $this->dispatcher();

    }

    public function dispatcher()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (Collector $routes) {
            foreach ($this->routes as $value) {
                $routes->addRoute(
                    $value['method'],
                    AppFactory::getPrefix().$value['url'],
                    ['controller' => $value['controller'],
                        'action' => $value['action'], 'bundle' => $value['bundle'] ]
                );
            }
        });
    }
}
