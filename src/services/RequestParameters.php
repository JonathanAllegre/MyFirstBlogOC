<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 26/03/2018
 * Time: 19:33
 */

namespace App\services;

class RequestParameters
{
    private $controller;
    private $action;
    private $bundle;
    private $parameters;


    /**
     * RequestParameters constructor.
     * @param $parameters
     */
    public function __construct($parameters)
    {
        if (!empty($parameters[2])) {
            $this->parameters = $parameters[2];
        }

        $this->controller = $parameters[1]['controller'];
        $this->action = $parameters[1]['action'];
        $this->bundle = $parameters[1]['bundle'];
    }

    /**
     * @return string
     */
    public function getController():string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction():string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getBundle():string
    {
        return $this->bundle;
    }

    /**
     * @return string
     */
    public function getParameters($key):string
    {
        return $this->parameters[$key];
    }
}
