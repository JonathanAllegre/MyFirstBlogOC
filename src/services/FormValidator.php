<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 22/03/2018
 * Time: 19:58
 */

namespace App\services;

use App\Entity\CommentEntity;
use App\Manager\AppManager;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class FormValidator extends AppFactory
{
    /**
     * @param $email
     * @param $required
     * @return array
     */
    public function validateEmailField($email, $required)
    {
        if ($required) {
            if (empty($email)) {
                $error = 1;
                $errorTitle = "Le Champ e-mail ne doit pas être vide";
                $data = $email;

                return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
            } else {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 0;
                    $errorTitle = "";
                    $data = $email;

                    return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
                } else {
                    $error = 1;
                    $errorTitle = "Erreur dans l'adresse e-mail";
                    $data = $email;

                    return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
                }
            }
        }

        if (!$required) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 0;
                $errorTitle = "";
                $data = $email;

                return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
            } else {
                $error = 1;
                $errorTitle = "Erreur dans l'adresse e-mail";
                $data = $email;

                return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
            }
        }
    }

    public function sanitizeString($string, $fieldName, $required = null)
    {
        if ($required) {
            if (empty($string)) {
                $error = 1;
                $errorTitle = "Le champ ".$fieldName." ne doit pas être vide";
                $data = $string;
            } else {
                $error = 0;
                $errorTitle = "";
                $data = $newstr = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            }
        }

        if (!$required) {
            $error = 0;
            $errorTitle = "";
            $data = $newstr = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        }

        return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
    }

    public function sanitizeSpecialChars($string, $fieldName, $required = null)
    {
        if ($required) {
            if (empty($string)) {
                $error = 1;
                $errorTitle = "Le champ ".$fieldName." ne doit pas être vide";
                $data = $string;
            } else {
                $error = 0;
                $errorTitle = "";
                $data = $newstr = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
            }
        }

        if (!$required) {
            $error = 0;
            $errorTitle = "";
            $data = $newstr = filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
    }

    public function validateContactForm(Request $request, Flash $flash)
    {
        $error = 0;

        // CHECK ALL FIELDS
        $name = $this->sanitizeString($request->request->get('name'), 'Nom', true);
        if ($name['error']) {
            $error = 1;
            $flash->set('warning', $name['errorTitle']);
        }

        $email = $this->validateEmailField($request->request->get('email'), true);
        if ($email['error']) {
            $error = 1;
            $flash->set('warning', $email['errorTitle']);
        }

        $message = $this->sanitizeString($request->request->get('message'), 'message', true);
        if ($message['error']) {
            $error = 1;
            $flash->set('warning', $message['errorTitle']);
        }

        return array(
            'error' => $error,
            'name' => $name['data'],
            'email' => $email['data'],
            'message' => $message['data'],
        );
    }

    public function validateRegisterUser(AppManager $manager, Request $request, Flash $flash)
    {
        $error = 0;
        // CHECK ALL FIELDS
        $lastName = $this->sanitizeString($request->request->get('last_name'), 'nom', true);
        if ($lastName['error']) {
            $error = 1;
            $flash->set('warning', $lastName['errorTitle']);
        }

        $firstName = $this->sanitizeString($request->request->get('first_name'), 'Prénom', true);
        if ($firstName['error']) {
            $error = 1;
            $flash->set('warning', $firstName['errorTitle']);
        }

        $email = $this->validateEmailField($request->request->get('email'), true);
        if ($email['error']) {
            $error = 1;
            $flash->set('warning', $email['errorTitle']);
        }

        $password = $this->sanitizeString($request->request->get('password'), "Mot de passe", true);
        if ($password['error']) {
            $error = 1;
            $flash->set('warning', $password['errorTitle']);
        }

        // CHECK IF EMAIL ADRESS EXIST
        $userManager = $manager->getUserManager();
        $exist =$userManager->checkExistMail($email['data']);
        if ($exist >= 1) {
            $error = 1;
            $flash->set('warning', 'Cette adresse e-mail existe déjà');
        }

        return array(
            'error' => $error,
            'last_name' => $lastName['data'],
            'first_name' => $firstName['data'],
            'email' => $email['data'],
            'password' =>$password['data'],
        );
    }

    public function validateDeleteUser(Request $request, Session $session, Flash $flash)
    {
        $error = 0;
        // CHECK TOKEN $POST
        $tokenSend = $this->sanitizeString($request->request->get('token'), 'tokenSend', true);
        if ($tokenSend['error']) {
            $error = 1;
            $flash->set('warning', $tokenSend['errorTitle']);
        }

        // CHECK IF TOKEN POST AND TOKEN SESSION ==
        if ($tokenSend['data'] != $session->get('myToken')) {
            $error = 1;
            $flash->set('warning', 'Erreur dans la validation des jetons');
        }

        return $error;
    }

    public function validateCommentForm(array $comment, Flash $flash, AppManager $manager, $token, Session $session)
    {
        $error = 0;
        // CHECK CONTENT
        $content = $this->sanitizeString($comment['content'], "message", true);
        if ($content['error']) {
            $error = 1;
            $flash->set('warning', $content['errorTitle']);
        }


        //CHECK ID POST ( IF IS INTEGER )
        if (!is_numeric($comment['id_post'])) {
            $error = 1;
            $flash->set('warning', "Erreur dans l'identifiant du post (id non entier)");
        }

        //CHECK ID POST ( IF ID POST EXIST )
        if (!$manager->getPostManager()->read($comment['id_post'])) {
            $error = 1;
            $flash->set('warning', "Erreur dans l'identifiant du post (id Inconnu)");
        }

        //CHECK IF USER IS NOT EMPTY
        if (!$comment['id_user']) {
            $error = 1;
            $flash->set('warning', "Vous n'êtes pas connecté");
        }

        ////CHECK TOKEN
        if ($token != $session->get('myToken')) {
            $error = 1;
            $flash->set('warning', "Erreur de token");
        }


        return array(
            'error' => $error,
            'content' => $content['data']
        );
    }
}
