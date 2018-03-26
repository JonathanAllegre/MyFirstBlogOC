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

class AppController
{
    private $config;
    private $http;


    public function __construct(AppFactory $app, Request $http)
    {
        $this->config = $app->getConfig();
        $this->http = $http;
    }

    public function render($path, $var = null)
    {
        $templatesFolder = $this->config->getTwigTemplates();

        $loader = new Twig_Loader_Filesystem($this->config->getRootPath() . $templatesFolder);

        $cache = false;
        if ($this->config->getTwigCache()) {
            $cache = $this->config->getRootPath() . $this->config->getTwigCache();
        }


        $twig = new Twig_Environment($loader, array(
            'cache' => $cache,
        ));

        // Add Global Objet LinkBuilder
        $twig->addGlobal('LinkBuilder', new LinkBuilder());

        $prefix = $this->config->getPrefix();
        if ($this->config->getPrefix() !== '/') {
            $prefix = $this->config->getPrefix().'/';
        }

        $request = $this->http->createFromGlobals();
        $httpHost = $request->server->get('HTTP_HOST');

        // DEFAULT VARIABLES
        $variables  = array(
            'publicFolder' => 'http://' . $httpHost  . $prefix . "public",
            'rootPath' => $this->config->getPrefix()
        );

        // MERGE VAR IF NOT EMPTY $VAR
        if ($var) {
            $variables = array_merge($variables, $var);
        }

        return $twig->render($path, $variables);
    }
}
