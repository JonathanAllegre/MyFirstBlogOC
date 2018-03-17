<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 14/03/2018
 * Time: 20:47
 */

namespace App\services;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Dotenv\Dotenv;

class Config
{
    private $env;
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPass;
    private $prefix;
    private $twigCache;
    private $twigTemplates;
    private $rootPath;


    /**
     * Config constructor.
     * @param $root
     */
    public function __construct($root)
    {
        // load .env
        $dotenv = new Dotenv();
        $dotenv->load($root.'/.env');

        // load var .env
        $this->env    = getenv('APP_ENV');
        $this->dbHost = getenv('DB_HOST');
        $this->dbName = getenv('DB_NAME');
        $this->dbUser = getenv('DB_USER');
        $this->dbPass = getenv('DB_PASS');

        // rootPath
        $this->rootPath = $root;
        $this->setYaml();
    }


    private function setYaml()
    {
        // load .yaml
        $yaml = new Yaml();
        $config = $yaml->parseFile($this->rootPath.'/config/app.yaml');
        $twig =  $yaml->parseFile($this->rootPath.'/config/twig.yaml');

        // load var .yaml
        $this->prefix = $config[$this->env]['prefix'];
        $this->twigCache = $twig[$this->env]['cache'];
        $this->twigTemplates = $twig[$this->env]['templates'];
    }

    /**
     * @return array|false|string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return array|false|string
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @return array|false|string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return array|false|string
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @return array|false|string
     */
    public function getDbPass()
    {
        return $this->dbPass;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return mixed
     */
    public function getTwigCache()
    {
        return $this->twigCache;
    }

    /**
     * @return mixed
     */
    public function getTwigTemplates()
    {
        return $this->twigTemplates;
    }

    /**
     * @return mixed
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }
}
