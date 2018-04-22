<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/03/2018
 * Time: 10:50
 */

namespace App\controller\error;

use App\controller\AppController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AppController
{
    public function notFound()
    {
        $reponse = new Response($this->render('error/notFound.html.twig'));

        $reponse->send();
    }
}
