<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 22/03/2018
 * Time: 19:58
 */

namespace App\services;

use App\Manager\AppManager;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Request;

class FormValidator extends AppFactory
{
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
                $errorTitle = "Le champ ".$fieldName." ne doit pas etre vide";
                $data = $string;
            } else {
                $error = 0;
                $errorTitle = "";
                $data = $newstr = filter_var($string, FILTER_SANITIZE_STRING);
            }
        }

        if (!$required) {
            $error = 0;
            $errorTitle = "";
            $data = $newstr = filter_var($string, FILTER_SANITIZE_STRING);
        }

        return array("error" => $error, "errorTitle" => $errorTitle, "data" => $data);
    }

    public function validateContactForm()
    {
        $request = $this->getRequest();

        $response = array('error' => 0, 'errorTitle' => '');
        // Stop Robots
        $adresse = $request->request->get('adresse');
        if (!empty($adresse)) {
            $error = 1;
            $response = array(
                'error' => 1,
                'errorTitle' => "Vous êtes un robot !",
            );
        }

        // Verif Empty
        if (empty($request->request->get('name'))) {
            $error = 1;
            $response = array(
                'error' => 1,
                'errorTitle' => "Vous devez remplir le champ Name"
            );
        }
        if (empty($request->request->get('message'))) {
            $error = 1;
            $response = array(
                'error' => 1,
                'errorTitle' => "Vous devez remplir le champ Message"
            );
        }

        // Validate email
        $email = $this->validateEmailField($request->request->get('email'));
        if ($email['statut'] == 1 && empty($adresse)) {
            $error = 1;
            $response = array(
                'error' => 1,
                'errorTitle' => "Erreur dans la validation du mail"
            );
        }

        return $response;
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
}
