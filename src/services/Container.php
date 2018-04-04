<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 26/03/2018
 * Time: 19:29
 */

namespace App\services;

use App\services\Sessions\Flash;
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Session;

class Container extends AppFactory
{
    /**
     * @param $requestParameters
     * @return \DI\Container
     * @throws \Exception
     */
    public function createConfig($requestParameters)
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->addDefinitions([
            RequestParameters::class => \DI\create()->constructor($requestParameters),
            Flash::class => \DI\create()->constructor(\DI\get(Session::class)),
            CheckPermissions::class => \DI\create()->constructor(
                \DI\get(Session::class),
                \DI\get(Flash::class)
            ),
        ]);

        return $containerBuilder->build();
    }
}
