<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 26/03/2018
 * Time: 19:29
 */

namespace App\services;

use DI\ContainerBuilder;

class Container extends AppFactory
{
    public function createConfig($requestParameters)
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->addDefinitions([
            RequestParameters::class => \DI\create()->constructor($requestParameters)
        ]);

        return $containerBuilder->build();
    }
}
