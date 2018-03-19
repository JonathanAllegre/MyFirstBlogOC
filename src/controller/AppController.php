<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:44
 */

namespace App\controller;

use App\services\AppFactory;
use App\services\Config;
use App\services\Twig;
use Twig_Environment;
use Twig_Loader_Filesystem;

class AppController extends AppFactory
{
    public function render($path, $var = null)
    {
        $config = $this->getConfig();
        $this->cache = $config->getTwigCache();
        $this->templatesFolder = $config->getTwigTemplates();

        $this->loader = new Twig_Loader_Filesystem($config->getRootPath() . $config->getTwigTemplates());

        $this->cache = false;

        if ($config->getTwigCache()) {
            $this->cache = $config->getRootPath() . $config->getTwigCache();
        }

        $twig = new Twig_Environment($this->loader, array(
            'cache' => $this->cache,
        ));


        if ($var === null) {
            return $twig->render($path);
        } else {
            return $twig->render($path, $var);
        }
    }
}
