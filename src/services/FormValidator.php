<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 22/03/2018
 * Time: 19:58
 */

namespace App\services;

class FormValidator extends AppFactory
{
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
}
