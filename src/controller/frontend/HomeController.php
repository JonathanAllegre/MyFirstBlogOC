<?php


namespace App\controller\frontend;

use App\controller\AppController;
use App\services\FormValidator;
use App\services\Mailer;
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
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $error = 0;

        // Stop Robots
        $adresse = $request->request->get('adresse');
        if (!empty($adresse)) {
            $error = 1;
            $response->setContent(json_encode(array(
               'error' => 1,
               'errorTitle' => "Vous êtes un robot !",
            )));
        }


        // Validate email
        $email = FormValidator::validateEmailField($request->request->get('email'));
        if ($email['statut'] == 1 && empty($adresse)) {
            $error = 1;
            $response->setContent(json_encode(array(
                'error' => 1,
                'errorTitle' => "Erreur dans la validation du mail"
            )));
        }


        // If No errors
        if ($error == 0 && empty($adresse)) {
            // Get Datas and filter
            $name = FormValidator::sanitizeString($request->request->get('name'));
            $message = FormValidator::sanitizeString($request->request->get('message'));
            $subject = "Contact MyFirstBlogOc";

            // Format message
            $text = " Message de ".$name."\r\n\r\n".$message;

            // Call mailer class
            $mail = new Mailer($email['email'], $subject, $text);

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
