<?php


namespace App\controller\frontend;

use App\controller\AppController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AppController
{
    public function index($vars = null)
    {

        // TEST //

        $retour = "Message bien envoy";
        $rmm = json_encode(array('data' => $retour,));

        var_dump($rmm);


        $reponse = new Response($this->render('/front/Home/index.html.twig', [
            'name' => "Jonjon",
            'var'  => $vars
        ]));
        $reponse->send();
    }

    public function sendMailContact()
    {
        $request = $this->getRequest();

        // Get $_POST[]
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $message = $request->request->get('message');

        // Send Request in form secure

        // Send secure fields to mailer



        $retour = "Une erreur est survenue";
        if (mail('jonathan.allegre258@orange.fr', 'Nouveau Contact '. $name, $message. $email)) {
            $retour = "Message bien envoyé";
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'data' => $retour,
            'status' => 1
        )));

        $response->headers->set('Content-Type', 'application/json');
        $response->send();
    }
}
