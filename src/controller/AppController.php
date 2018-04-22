<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:44
 */

namespace App\controller;

use App\services\AppFactory;

use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\RequestParameters;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Environment;
use Twig_Loader_Filesystem;

class AppController
{
    private $config;
    private $host;
    private $session;
    private $app;

    public function __construct(
        AppFactory $app,
        Session $session,
        RequestParameters $requestParameters,
        CheckPermissions $checkPermissions,
        LinkBuilder $linkBuilder
    ) {
        $this->config = $app->getConfig();
        $this->host = $app->getRequest()->server->get('HTTP_HOST');
        $this->session = $session;
        $this->app = $app;

        // REDIRECT IF USER IS NOT ADMIN
        if ($requestParameters->getBundle() && $requestParameters->getBundle() === "backend") {
            if (!$checkPermissions->isAdmin()) {
                $response = new RedirectResponse($linkBuilder->getLink('Home'));
                return $response->send();
            }
        }
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
        $twig->addGlobal('Flash', new Flash($this->session));
        $twig->addGlobal('Session', new Session());

        $prefix = $this->config->getPrefix();
        if ($this->config->getPrefix() !== '/') {
            $prefix = $this->config->getPrefix().'/';
        }


        // DEFAULT VARIABLES
        $variables  = array(
            'publicFolder' => 'http://' . $this->host  . $prefix . "public",
            'rootPath' => $this->config->getPrefix()
        );

        // MERGE VAR IF NOT EMPTY $VAR
        if ($var) {
            $variables = array_merge($variables, $var);
        }

        return $twig->render($path, $variables);
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getApp(): AppFactory
    {
        return $this->app;
    }
}
