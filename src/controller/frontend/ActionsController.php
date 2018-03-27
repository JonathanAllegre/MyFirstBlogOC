<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 27/03/2018
 * Time: 17:11
 */

namespace App\controller\frontend;



use App\controller\AppController;
use Symfony\Component\HttpFoundation\Response;

class ActionsController extends AppController
{
    public function registerUser()
    {



        /// SI NOT OK
        $reponse = new Response($this->render('/front/Action/registerUser.html.twig', [
            'name' => "Jonjon"
        ]));
        $reponse->send();


        //// SI OK ON REDIRIGE VERS user/account/view
    }
}
