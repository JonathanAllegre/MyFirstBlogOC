<?php


namespace App\controller\frontend;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index()
    {
        $reponse = new Response('Salut');

        $reponse->send();
    }
}
