<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 16:48
 */

namespace App\controller\backend;

use App\controller\AppController;
use App\Manager\AppManager;
use App\services\AppFactory;
use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends AppController
{
    public function index(AppManager $manager)
    {

        // GET POST LIST
        $listPost = $manager->getPostManager()->getAllPost(5);

        // GET COMMENT LIST NO VALIDATE
        $noValidateComments = $manager->getCommentManager()->getCommentInStatut(1);
        $reponse = new Response($this->render('/back/Home/index.html.twig', [
            'active' => 'home',
            'posts' => $listPost,
            'noValidateComments' => $noValidateComments
        ]));
        return $reponse->send();
    }
}
