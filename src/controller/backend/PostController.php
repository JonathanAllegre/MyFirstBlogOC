<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 19:04
 */

namespace App\controller\backend;

use App\controller\AppController;
use App\Manager\AppManager;
use App\services\LinkBuilder;
use App\services\PostServices\AddPost;
use App\services\PostServices\UpdatePost;
use App\services\RequestParameters;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends AppController
{
    public function add(LinkBuilder $linkBuilder, Flash $flash)
    {

        // IF METHOD != POST ( IF FORM POST IS NOT SEND )
        if ($this->getApp()->getRequest()->server->get('REQUEST_METHOD') != "POST") {
            $reponse = new Response($this->render('/back/Post/add.html.twig', [
                'active' => "articles",
                'myToken' => $this->getSession()->get('myToken'),
            ]));
            return $reponse->send();
        }

        // IF FORM IS SEND ( IF REQUEST == POST  // GET $POST
        $post = $this->getApp()->getRequest()->request->all();
        $addPost = new AddPost();
        if (!$lastId = $addPost->add($this->getSession(), $post)) {
            $response = new RedirectResponse($linkBuilder->getLink('PostAdminAdd'));
            return $response->send();
        }

        // REDIRECT TO POST/UPDATE/{article_id}
        $flash->set('success', 'Votre article a bien été enregistré. Vous pouvez maintenant le modifier');
        $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
            'article_id' => $lastId
        ]));
        return $response->send();
    }


    public function update(
        LinkBuilder $linkBuilder,
        RequestParameters $parameters,
        AppManager $manager,
        Flash $flash
    ) {

        // GET ID POST AND ARTICLE
        $articleId = $parameters->getParameters('article_id');
        $post = $manager->getPostManager()->read($articleId);

        // IF $POST DON'T EXIST
        if (!$post) {
            $flash->set('warning', "L'article demandé n'existe pas");
            $response = new RedirectResponse($linkBuilder->getLink('HomeAdmin'));
            return $response->send();
        }

        // ------------- IF METHOD = POST ( IF FORM POST IS SEND ) ---------
        if ($this->getApp()->getRequest()->server->get('REQUEST_METHOD') == "POST") {
            // GET $FORM DATA
            $formData = $this->getApp()->getRequest()->request->all();

            // WE CALL UPDATEPOST CLASS
            $updatePost = new UpdatePost();
            $post = $updatePost->update($formData, $this->getSession(), $post);

            // IF ERROR
            if (!$post) {
                $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
                    'article_id' => $articleId
                ]));
                return $response->send();
            }
        }

        $reponse = new Response($this->render('/back/Post/update.html.twig', [
                'active' => "articles",
                'post' => $post,
                'myToken' => $this->getSession()->get('myToken'),
        ]));
        return $reponse->send();
    }

    public function delete(
        LinkBuilder $linkBuilder,
        Flash $flash,
        AppManager $appManager
    ) {

        // GET POST DATA
        $formData = $this->getApp()->getRequest()->request->all();

        // CHECK IF TOKENS MATCH
        if ($formData['myToken'] != $this->getSession()->get('myToken')) {
            $flash->set('warning', 'Erreur de token');
            $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
                'article_id' => $formData['id_post'],
            ]));
            return $response->send();
        }

        // IF NO ERRORS WE DELETE THE POST
        $manager = $appManager->getPostManager()->delete($formData['id_post']);

        // IF ERROR WE REDIRECT TO ADMIN UPDATE
        if (!$manager) {
            $flash->set("warning", "Erreur lors de la supression de l'article");
            $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
                'article_id' => $formData['id_post'],
            ]));
            return $response->send();
        }

        // IF NO ERRORS WE REDIRECT TO HOME ADMIN
        $flash->set("success", "Votre article à bien été supprimé");
        $response = new RedirectResponse($linkBuilder->getLink('HomeAdmin'));
        return $response->send();
    }

    public function allPost(AppManager $manager)
    {
        $posts = $manager->getPostManager()->getAllPost();
        $reponse = new Response($this->render('/back/Post/allPost.html.twig', [
            'active' => "articles",
            'posts' => $posts,
        ]));
        return $reponse->send();
    }
}
