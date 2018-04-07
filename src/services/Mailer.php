<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 24/03/2018
 * Time: 11:16
 */

namespace App\services;

class Mailer extends AppFactory
{
    /**
     * @param $from
     * @param $subject
     * @param $message
     * @return int
     */
    public function sendMail($from, $subject, $message):int
    {
        // GET VAR
        $sendTo = $this->getConfig()->getMail();
        $header = 'From: ' . $from;

        // SEND MAIL
        if (mail($sendTo, $subject, $message, $header)) {
            return $error = 0;
        }
        return $error = 1;
    }
}
