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
        if ($session->get('user')) {
            // GET USER SESSION
            $userSession = $session->get('user');

            // GET USER OBJECT
            $user = $manager->getUserManager()->getUser($userSession['id']);

            // SET RESPONSE
            $reponse = new Response($this->render('/front/user/myAccount.html.twig', [
                'user' => $user
            ]));
            $reponse->send();
        } else {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            $response->send();
        }
    }
}
