<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/03/2018
 * Time: 10:50
 */

namespace App\controller\error;

use Symfony\Component\HttpFoundation\Response;

class ErrorController
{
    public function notFound()
    {
        $reponse = new Response('page non trouvée');

        $reponse->send();
    }
}
