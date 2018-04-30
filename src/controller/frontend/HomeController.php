<?php


namespace App\controller\frontend;

use App\controller\AppController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AppController
{

    /**
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index()
    {


        // DEPENDENCY
        $validator = $this->container->getFormValidator();
        $mailer = $this->container->getMailer();
        $flash = $this->container->getFlash();

        // IF METHOD != POST ( IF FORM CONTACT NOT SEND )
        if ($this->container->getRequest()->server->get('REQUEST_METHOD') != "POST") {
            $reponse = new Response($this->render('/front/Home/index.html.twig', [
                'active' => 'home',
            ]));
            return $reponse->send();
        }

        // IF FORM CONTACT IS SEND
        // GET REQUEST
        $request = $this->container->getRequest();

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
