<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 20/04/2018
 * Time: 23:54
 */

namespace App\services;

use App\services\PostServices\AddPost;
use App\services\PostServices\UpdatePost;
use App\services\Sessions\Flash;
use DI\ContainerBuilder;

class AppService
{

    /**
     * @return \DI\Container
     * @throws \Exception
     */
    private function container()
    {
        $definition = __DIR__.'/../../';
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->addDefinitions($definition.'config/configServices.php');

        return $containerBuilder->build();
    }


    /**
     * @return AddPost
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getAddPost():AddPost
    {
        return $this->container()->get(AddPost::class);
    }

    /**
     * @return UpdatePost|mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getUpdatePost()
    {
        return $this->container()->get(UpdatePost::class);
    }

    /**
     * @return LinkBuilder
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getLinkBuilder():LinkBuilder
    {
        return $this->container()->get(LinkBuilder::class);
    }

    /**
     * @return Flash
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getFlash():Flash
    {
        return $this->container()->get(Flash::class);
    }
}
