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

use Symfony\Component\HttpFoundation\Session\Session;

class AppService extends Container
{


    /**
     * @return AddPost
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getAddPost()
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

    /**
     * @return mixed|Session
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getSession()
    {
        return $this->container()->get(Session::class);
    }

    /**
     * @return FormValidator|mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getFormValidator()
    {
        return $this->container()->get(FormValidator::class);
    }

    /**
     * @return Mailer|mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */

    /**
     * @return CheckPermissions|mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function getCheckPermission()
    {
        return $this->container()->get(CheckPermissions::class);
    }
}
