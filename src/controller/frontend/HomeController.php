<?php


namespace App\controller\frontend;

use Symfony\Component\HttpFoundation\Response;
use App\services\Twig;

class HomeController
{
    public function index(Twig $twig)
    {
        $reponse = new Response($twig->renderView('/front/Home/index.html.twig', [
            'name' => "Jonjon"
        ]));
        $reponse->send();
    }
}
