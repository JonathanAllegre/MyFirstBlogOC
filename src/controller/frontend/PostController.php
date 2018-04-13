<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/04/2018
 * Time: 10:17
 */

namespace App\controller\frontend;

use App\controller\AppController;
use App\Entity\CommentEntity;
use App\Manager\AppManager;
use App\services\CheckPermissions;
use App\services\FormValidator;
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
        CheckPermissions $checkPermissions,
        FormValidator $validator
    ) {

        // GET POST ID
        $postId = $requestParameters->getParameters('id_article');

        // LOAD POST IN DB
        $post = $appManager->getPostManager()->read($postId, 'true');

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

        // ------------- IF METHOD = POST ( IF FORM COMMENT IS SENT ) ---------
        if ($this->getApp()->getRequest()->server->get('REQUEST_METHOD') == "POST") {
            $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

            // GET $FORM DATA
            $comment = array(
                'message' => $this->getApp()->getRequest()->request->get('message'),
                'created' => $date->format('Y-m-d H:i:s'),
                'modified' => $date->format('Y-m-d H:i:s'),
                'content' => $this->getApp()->getRequest()->request->get('message'),
                'id_post' => $requestParameters->getParameters('id_article'),
                'id_comment_statut' => 1,
                'id_user' => $this->getSession()->get('user')['id'],
            );

            // FORM VALIDATOR
            $valideComment = $validator->validateCommentForm(
                $comment,
                $flash,
                $appManager,
                $this->getApp()->getRequest()->request->get('token'),
                $this->getSession()
            );

            // IF NO ERROR IN VALIDATION
            if (!$valideComment['error']) {
                // WE REPLACE CONTENT MESSAGE BY SANITIZE CONTENT
                $comment['content'] = $valideComment['content'];

                // BUILD ENTITY
                $comment = new CommentEntity($comment);

                // IF NO ERROR WE SEND FLASH SUCCESS
                if ($appManager->getCommentManager()->create($comment)) {
                    $flash->set(
                        "success",
                        "Merci d'avoir commenté cet article. 
                        Une vérification du commentaire sera éffectuée avant d'être publié"
                    );
                }
            }
        }


        // WE SEND THE REPONSE
        $reponse = new Response($this->render('/front/Post/read.html.twig', [
            'active' => 'articles',
            'post' => $post,
            'allPosts' => $allPosts,
            'userInSession' => $userInSession,
            'myToken' => $this->getSession()->get('myToken')

        ]));
        return $reponse->send();
    }
}
