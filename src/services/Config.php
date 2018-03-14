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

    /**
     * Config constructor.
     */
    public function __construct()
    {
        // load .env
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');

        // load var .env
        $this->env    = getenv('APP_ENV');
        $this->dbHost = getenv('DB_HOST');
        $this->dbName = getenv('DB_NAME');
        $this->dbUser = getenv('DB_USER');
        $this->dbPass = getenv('DB_PASS');

        $this->setPrefix();
    }

    private function setPrefix()
    {
        // load .yaml
        $config = Yaml::parseFile(__DIR__.'/../../config/app.yaml');

        // load var .yaml
        $this->prefix = $config[$this->env]['prefix'];
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
}
