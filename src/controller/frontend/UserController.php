<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/2018
 * Time: 14:41
 */

namespace App\controller\frontend;

use App\controller\AppController;
use App\Manager\AppManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\services\LinkBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends AppController
{
    public function myAccount(Session $session, LinkBuilder $linkBuilder, AppManager $manager)
    {
        // IF $SESSION.USER DON'T EXIST WE REDIRECT TO HOME
        if (!$session->get('user')) {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            $response->send();
        }

        // GET USER SESSION & TOKEN
        $userSession = $session->get('user');
        $token = $session->get('myToken');

        // GET USER OBJECT
        $user = $manager->getUserManager()->getUserById($userSession['id']);


        // SET RESPONSE
        $reponse = new Response($this->render('/front/user/myAccount.html.twig', [
            'user' => $user,
            'token' => $token,
        ]));
        $reponse->send();
    }
}
