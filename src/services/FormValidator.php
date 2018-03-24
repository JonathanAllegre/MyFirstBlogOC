<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 22/03/2018
 * Time: 19:58
 */

namespace App\services;

class FormValidator
{
    public static function validateEmailField($email)
    {
        $error = 1;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 0;
        }

        $var = array(
            'email' => $email,
            'statut' => $error,
        );

        return $var;
    }

    public static function sanitizeString($string)
    {
        $newstr = filter_var($string, FILTER_SANITIZE_STRING);

        return $newstr;
    }
}
