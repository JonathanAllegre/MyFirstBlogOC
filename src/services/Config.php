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
    private $mail;
    private $imgBlogFolder;


    /**
     * Config constructor.
     */
    public function __construct()
    {

        // rootPath
        $this->rootPath = __DIR__.'/../../';

        // load .env
        $dotenv = new Dotenv();
        $dotenv->load($this->rootPath.'/.env');

        // load var .env
        $this->env    = getenv('APP_ENV');
        $this->dbHost = getenv('DB_HOST');
        $this->dbName = getenv('DB_NAME');
        $this->dbUser = getenv('DB_USER');
        $this->dbPass = getenv('DB_PASS');
        $this->mail = getenv('MAIL_ADRESS');

        $this->setYaml();
    }


    private function setYaml():void
    {
        // load .yaml
        $yaml = new Yaml();
        $config = $yaml->parseFile($this->rootPath.'/config/app.yaml');
        $twig =  $yaml->parseFile($this->rootPath.'/config/twig.yaml');

        // load var .yaml
        $this->prefix = $config[$this->env]['prefix'];
        $this->imgBlogFolder = $config[$this->env]['imgBlogFolder'];
        $this->twigCache = $twig[$this->env]['cache'];
        $this->twigTemplates = $twig[$this->env]['templates'];
    }

    /**
     * @return array|false|string
     */
    public function getEnv():string
    {
        return $this->env;
    }

    /**
     * @return array|false|string
     */
    public function getDbHost():string
    {
        return $this->dbHost;
    }

    /**
     * @return array|false|string
     */
    public function getDbName():string
    {
        return $this->dbName;
    }

    /**
     * @return array|false|string
     */
    public function getDbUser():string
    {
        return $this->dbUser;
    }

    /**
     * @return array|false|string
     */
    public function getDbPass():string
    {
        return $this->dbPass;
    }

    /**
     * @return mixed
     */
    public function getPrefix():string
    {
        return $this->prefix;
    }

    /**
     * @return mixed
     */
    public function getTwigCache():string
    {
        return $this->twigCache;
    }

    /**
     * @return mixed
     */
    public function getTwigTemplates():string
    {
        return $this->twigTemplates;
    }

    /**
     * @return mixed
     */
    public function getRootPath():string
    {
        return $this->rootPath;
    }

    /**
     * @return array|false|string
     */
    public function getMail():string
    {
        return $this->mail;
    }

    /**
     * @return mixed
     */
    public function getImgBlogFolder()
    {
        return $this->imgBlogFolder;
    }
}
