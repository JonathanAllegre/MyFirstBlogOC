<?php


namespace App\controller\frontend;

use App\controller\AppController;
use App\services\AppFactory;
use App\services\FormValidator;
use App\services\Mailer;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AppController
{
    /**
     * @param AppFactory $appFactory
     * @param FormValidator $validator
     * @param Flash $flash
     * @param Mailer $mailer
     * @return Response
     */
    public function index(AppFactory $appFactory, FormValidator $validator, Flash $flash, Mailer $mailer)
    {

        // IF METHOD != POST ( IF FORM CONTACT NOT SEND )
        if ($appFactory->getRequest()->server->get('REQUEST_METHOD') != "POST") {
            $reponse = new Response($this->render('/front/Home/index.html.twig', [
                'active' => 'home',
            ]));
            return $reponse->send();
        }

        // IF FORM CONTACT IS SEND
        // GET REQUEST
        $request = $appFactory->getRequest();

        // VALIDATE FORM
        $validate = $validator->validateContactForm($request, $flash);

        // IF ERRORS IN VALIDATE FORM
        if ($validate['error']) {
            $reponse = new Response($this->render('/front/Home/index.html.twig', [
                'post_name' => $validate['name'],
                'post_email' => $validate['email'],
                'post_message' => $validate['message'],
            ]));
            return $reponse->send();
        }

        // IF NO ERRORS WE SEND MAIL
        // BUILD MAIL VAR
        $name = $validate['name'];
        $message = $validate['message'];
        $subject = "Contact MyFirstBlogOc";
        $email = $validate['email'];

        // FORMATE THE BODY OF MESSAGE
        $text = " Message de ".$name."\r\n\r\n".$message;

        // CALL THE MAILER CLASS
        $send = $mailer->sendMail($email, $subject, $text);


        // IF ERROR IN MAILER
        if ($send) {
            $flash->set('warning', "Un problème est survenue lors de l'envoie du mail");
            $reponse = new Response($this->render('/front/Home/index.html.twig', [
                'post_name' => $validate['name'],
                'post_email' => $validate['email'],
                'post_message' => $validate['message'],
            ]));
            return $reponse->send();
        }

        // IF NO ERRORS WE DISPLAY HOME ADMIN
        $flash->set('success', "Votre Message a été correctement envoyé");
        $reponse = new Response($this->render('/front/Home/index.html.twig', [
            'active' => 'home',
        ]));
        return $reponse->send();
    }
}
