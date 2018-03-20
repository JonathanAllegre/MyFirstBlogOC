<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:44
 */

namespace App\controller;

use App\services\AppFactory;
use Twig_Environment;
use Twig_Loader_Filesystem;

class AppController extends AppFactory
{

    public function render($path, $var = null)
    {
        $config = $this->getConfig();
        $templatesFolder = $config->getTwigTemplates();

        $loader = new Twig_Loader_Filesystem($config->getRootPath() . $templatesFolder);

        $cache = false;
        if ($config->getTwigCache()) {
            $cache = $config->getRootPath() . $config->getTwigCache();
        }


        $twig = new Twig_Environment($loader, array(
            'cache' => $cache,
        ));


        if ($var === null) {
            return $twig->render($path);
        } else {
            return $twig->render($path, $var);
        }
    }
}
