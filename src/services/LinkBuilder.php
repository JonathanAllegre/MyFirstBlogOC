<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 25/03/2018
 * Time: 17:11
 */

namespace App\services;



use Symfony\Component\Yaml\Yaml;

class LinkBuilder extends AppFactory
{
    private $routes;
    private $prefix;


    public function __construct()
    {
        if ($this->routes === null) {
            $yaml = new Yaml();
            $this->routes = $yaml->parseFile(__DIR__.'/../../config/routes.yaml');
        }
        if ($this->prefix === null) {
            $this->prefix = $this->getConfig()->getPrefix();
        }
    }

    public function getLink($routeName, $parametres = null)
    {
        $route = $routeName;

        $allRoutes = $this->routes;

        if (array_key_exists($routeName, $allRoutes)) {
            $foundRoute = $allRoutes[$route];
        }

        if (isset($foundRoute)) {
            $urlRoute = $foundRoute['url'];
            if (!empty($parametres)) {
                foreach ($parametres as $key => $value) {
                    if (preg_match("/{" . $key . "}/", $urlRoute)) {
                        $urlRoute = str_replace("{" . $key . "}", $value, $urlRoute);
                    } else {
                        $urlRoute = "";
                    }
                }
                $urlRoute = $this->prefix . $urlRoute;
            } else {
                $urlRoute =  $this->prefix.$foundRoute['url'];
            }
            return $urlRoute;
        } else {
            return "";
        }
    }
}
