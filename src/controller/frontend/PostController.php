<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/04/2018
 * Time: 10:17
 */

namespace App\controller\frontend;

use App\controller\AppController;
use App\Manager\AppManager;
use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\RequestParameters;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AppController
{
    public function listPost(AppManager $manager)
    {
        $posts = $manager->getPostManager()->getAllPost();

        $reponse = new Response($this->render('/front/Post/listPost.html.twig', [
            'active' => 'articles',
            'posts' => $posts

        ]));
        return $reponse->send();
    }


    public function read(
        RequestParameters $requestParameters,
        AppManager $appManager,
        Flash $flash,
        LinkBuilder $linkBuilder,
        CheckPermissions $checkPermissions
    ) {

        // GET POST ID
        $postId = $requestParameters->getParameters('id_article');

        // LOAD POST IN DB
        $post = $appManager->getPostManager()->read($postId);

        // LOAD ALL POST FOR THE SIDEBAR
        $allPosts = $appManager->getPostManager()->getAllPost('10');

        // VERIFY IF POST HAVE A ONLINE STATUT
        if ($post->getIdStatutPost() == 2) {
            $flash->set('warning', "Vous n'avez pas accès à cet article");
            $response = new RedirectResponse($linkBuilder->getLink('PostList'));
            return $response->send();
        }

        // FOR COMMENT WE CHECK IF THE USER IS CONNECT
        $userInSession = ($checkPermissions->isConnect()) ? $this->getSession()->get('user') : null;

        $reponse = new Response($this->render('/front/Post/read.html.twig', [
            'active' => 'articles',
            'post' => $post,
            'allPosts' => $allPosts,
            'userInSession' => $userInSession

        ]));
        return $reponse->send();
    }
}
