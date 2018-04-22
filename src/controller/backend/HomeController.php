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
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AppController
{

    /**
     * @param AppManager $manager
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(AppManager $manager)
    {


        // GET POST LIST
        $listPost = $manager->getPostManager()->getAllPost(5);


        // GET COMMENT LIST NO VALIDATE
        $noValidateComments = $manager->getCommentManager()->getCommentInStatut(1);

        // GET USER LIST
        $users = $manager->getUserManager()->getAllUsers(10);

        $reponse = new Response($this->render('/back/Home/index.html.twig', [
            'active' => 'home',
            'posts' => $listPost,
            'users' => $users,
            'noValidateComments' => $noValidateComments
        ]));
        return $reponse->send();
    }
}
