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
    private $sendTo;
    private $subject;
    private $message;
    private $headers;
    private $error;


    /**
     * Mailer constructor.
     * @param $from
     * @param $subject
     * @param $message
     */
    public function __construct($from, $subject, $message)
    {
        $this->sendTo = $this->getConfig()->getMail();
        $this->headers = 'From: '.$from;
        $this->subject = $subject;
        $this->message = $message;

        $this->sendMail();
    }

    public function sendMail()
    {
        $this->error = 1;
        if (mail($this->sendTo, $this->subject, $this->message, $this->headers)) {
            $this->error = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
