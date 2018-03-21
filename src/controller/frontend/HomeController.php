<?php


namespace App\controller\frontend;

use App\controller\AppController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AppController
{
    public function index($vars = null)
    {
        $reponse = new Response($this->render('/front/Home/index.html.twig', [
            'name' => "Jonjon",
            'var'  => $vars
        ]));
        $reponse->send();
    }
}
