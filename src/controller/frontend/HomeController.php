<?php


namespace App\controller\frontend;

use App\controller\AppController;
use Symfony\Component\HttpFoundation\Response;


class HomeController extends AppController
{
    public function index()
    {
        $reponse = new Response($this->render('/front/Home/index.html.twig', [
            'name' => "Jonjon"
        ]));
        $reponse->send();
    }

    
    public function sendMailContact()
    {
        $request = $this->getRequest();

        $response      = new Response();
        $formValidator = new FormValidator();
        $response->headers->set('Content-Type', 'application/json');


        // Validate Form
        $retour = $formValidator->validateContactForm();
        $error = $retour['error'];

        // If Errors
        if ($error == 1) {
            $response->setContent(json_encode(array(
                'error' => 1,
                'errorTitle' => $retour['errorTitle'],
            )));
        }

        // If No errors
        if ($error == 0) {
            // Get Datas and filter
            $name = $formValidator->sanitizeString($request->request->get('name'));
            $message = $formValidator->sanitizeString($request->request->get('message'));
            $subject = "Contact MyFirstBlogOc";

            // Format message
            $text = " Message de ".$name."\r\n\r\n".$message;

            // Call mailer class
            $mail = new Mailer($request->request->get('email'), $subject, $text);

            // Get Errors
            $sendError = $mail->getError();

            // If errors response = KO
            if ($sendError == 1) {
                $response->setContent(json_encode(array(
                    'error' => 1,
                    'errorTitle' => "Erreur lors de l'envoie du mail",
                )));
            }

            // If no errors response = OK
            if ($sendError == 0) {
                $response->setContent(json_encode(array(
                    'retour' => "Message bien envoyé",
                    'error' => 0,
                    'errorTitle' => ""
                )));
            }
        }

        $response->send();
    }

}
