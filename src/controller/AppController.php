<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:44
 */

namespace App\controller;

use App\services\AppFactory;
use App\services\LinkBuilder;
use Symfony\Component\HttpFoundation\Request;
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

        // Add Global Objet LinkBuilder
        $twig->addGlobal('LinkBuilder', new LinkBuilder());

        $prefix = $config->getPrefix();
        if ($config->getPrefix() !== '/') {
            $prefix = $config->getPrefix().'/';
        }

        $http = new Request();
        $request = $http->createFromGlobals();
        $httpHost = $request->server->get('HTTP_HOST');

        // DEFAULT VARIABLES
        $variables  = array(
            'publicFolder' => 'http://' . $httpHost  . $prefix . "public",
            'rootPath' => $config->getPrefix()
        );

        // MERGE VAR IF NOT EMPTY $VAR
        if ($var) {
            $variables = array_merge($variables, $var);
        }

        return $twig->render($path, $variables);
    }
}
