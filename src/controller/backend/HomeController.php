<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 16:48
 */

namespace App\controller\backend;

use App\controller\AppController;
use App\services\AppFactory;
use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends AppController
{
    public function index(
        LinkBuilder $linkBuilder,
        CheckPermissions $checkPermissions
    ) {

        // IF USER IS NOT CONNECT OR IF USER DON'T HAVE PERMISION
        if (!$checkPermissions->isAdmin()) {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            return $response->send();
        }

        // IF USER IS CONNECT AND HAVE THE GOOD LEVEL AUTH
        $reponse = new Response($this->render('/back/Home/index.html.twig', [
            'active' => 'home',
        ]));
        return $reponse->send();
    }
}
