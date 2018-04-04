<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 16:48
 */

namespace App\controller\backend;


use App\controller\AppController;
use App\services\AppFactory;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AppController
{

    public function index(AppFactory $appFactory)
    {

        $reponse = new Response($this->render('/back/Home/index.html.twig'));
        return $reponse->send();

    }

}