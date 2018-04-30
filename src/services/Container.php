<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 26/03/2018
 * Time: 19:29
 */

namespace App\services;

use App\Manager\AppManager;
use App\services\Sessions\Flash;
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Container
 * @package App\services
 */
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

        $definition = __DIR__ . '/../../';
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->addDefinitions($definition . 'config/configServices.php');
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
        return $this->container()->get(flash::class);
    }

    /**
     * @return RequestParameters
     */
    public function getRequestParameters(): RequestParameters
    {
        return self::$requestParams;
    }

    /**
     * @return AppManager|mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getManager()
    {
        return $this->container()->get(AppManager::class);
    }
}
