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

    public function sendMailContact()
    {
        $request = $this->getRequest();

        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $message = $request->request->get('message');

        $response = "Une erreur est survenue";
        if (mail('jonathan.allegre258@orange.fr', 'Nouveau Contact '. $name, $message. $email)) {
            $response = "Message bien envoyé";
        }

        echo $response;
    }
}
