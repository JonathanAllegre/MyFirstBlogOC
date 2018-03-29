<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/2018
 * Time: 17:30
 */

namespace App\Manager;

use App\services\AppFactory;
use \PDO as PDO;

class AppManager
{
    private static $bdd;
    private $config;

    public function __construct(AppFactory $app)
    {
        $this->config = $app->getConfig();
    }

    /**
     * @return PDO
     */
    private function getBdd()
    {
        if (self::$bdd === null) {
            $optPdo =  array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

            self::$bdd =  new PDO(
                'mysql:host='.$this->config->getDbHost().';dbname='.$this->config->getDbName().'',
                ''.$this->config->getDbUser().'',
                ''.$this->config->getDbPass().'',
                $optPdo
            );
        }

        return self::$bdd;
    }

    /**
     * @param $manager
     * @return mixed
     */
    public function getManager($manager)
    {
        $manager = "App\\Manager\\".$manager;
        return new $manager($this->getBdd());
    }
}
