<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/03/2018
 * Time: 19:44
 */

namespace App\controller;

use App\services\AppService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig_Environment;
use Twig_Loader_Filesystem;

class AppController
{
    private $config;
    private $host;
    private $session;
    // DI
    public $container;


    /**
     * AppController constructor.
     * @param AppService $service
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function __construct(AppService $service)
    {
        $this->config = $service->getConfig();
        $this->host = $service->getRequest()->server->get('HTTP_HOST');
        $this->session = $service->getSession();
        $this->container = $service;

        $requestParameters = $service->getRequestParameters();
        $linkBuilder = $service->getLinkBuilder();
        $checkPermissions = $service->getCheckPermission();

        // REDIRECT IF USER IS NOT ADMIN
        if ($requestParameters->getBundle() && $requestParameters->getBundle() === "backend") {
            if (!$checkPermissions->isAdmin()) {
                $response = new RedirectResponse($linkBuilder->getLink('Home'));
                $response->send();
                exit();
            }
        }
    }


    /**
     * @param $path
     * @param null $var
     * @return string
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
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

        // ADD GLOBAL OBJECT FOR TWIG TEMPLATES
        $twig->addGlobal('LinkBuilder', $this->container->getLinkBuilder());
        $twig->addGlobal('Flash', $this->container->getFlash());
        $twig->addGlobal('Session', $this->session);

        $prefix = $this->config->getPrefix();
        if ($this->config->getPrefix() !== '/') {
            $prefix = $this->config->getPrefix().'/';
        }

        // DEFAULT VARIABLES FOR TEMPLATES
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
}
