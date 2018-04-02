<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:55
 */

namespace App\services;

use Symfony\Component\HttpFoundation\Request;

class AppFactory
{
    /**
     * @var object Config
     */
    private static $config;
    /**
     * @var object Request
     */
    private static $request;


    public function getConfig():Config
    {
        if (self::$config === null) {
            self::$config = new Config();
        }
        return self::$config;
    }

    public function getRequest():Request
    {
        if (self::$request === null) {
            $http = new Request();
            self::$request = $http->createFromGlobals();
        }
        return self::$request;
    }
}
