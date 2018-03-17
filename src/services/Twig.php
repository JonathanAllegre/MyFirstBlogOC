<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/03/2018
 * Time: 11:31
 */

namespace App\services;

use Twig_Loader_Filesystem;
use Twig_Environment;

class Twig
{
    private $cache;
    private $templatesFolder;
    private $loader;


    public function __construct(Config $config)
    {
        $this->cache = $config->getTwigCache();
        $this->templatesFolder = $config->getTwigTemplates();

        $this->loader = new Twig_Loader_Filesystem($config->getRootPath() . $config->getTwigTemplates());

        if ($config->getTwigCache()) {
            $this->cache = $config->getRootPath().$config->getTwigCache();
        } else {
            $this->cache = false;
        }
    }

    public function renderView($pathTemplate, array $var = null)
    {
        $twig = new Twig_Environment($this->loader, array(
                'cache' => $this->cache,
            ));

        return $twig->render($pathTemplate, $var);
    }
}
