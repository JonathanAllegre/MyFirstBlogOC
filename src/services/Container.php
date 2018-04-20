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
    private static $requestParams;

    /**
     * @param $requestParameters
     * @return \DI\Container
     * @throws \Exception
     */
    public function container($requestParameters = null)
    {
        if (self::$requestParams === null) {
            self::$requestParams = new RequestParameters($requestParameters);
        }

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->addDefinitions([
            Flash::class => \DI\create()->constructor(\DI\get(Session::class)),
            CheckPermissions::class => \DI\create()->constructor(
                \DI\get(Session::class),
                \DI\get(Flash::class)
            ),
        ]);
        if ($requestParameters) {
            $containerBuilder->addDefinitions([
                RequestParameters::class => \DI\create()->constructor($requestParameters)
            ]);
        }

        return $containerBuilder->build();
    }

    /**
     * @return Flash|mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getFlash()
    {
        return  $this->container()->get(flash::class);
    }

    /**
     * @return RequestParameters
     */
    public function getRequestParameters():RequestParameters
    {
        return self::$requestParams;
    }
}
