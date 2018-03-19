<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:55
 */

namespace App\services;

class AppFactory
{
    private static $config;


    public function getConfig()
    {
        if (self::$config === null) {
            self::$config = new Config();
            return self::$config;
        } else {
            return self::$config;
        }
    }
}
