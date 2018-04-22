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
use App\services\AppService;
use App\services\Container;
use App\services\PictureServices\DeletePicture;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends AppController
{

    /**
     * @param Container $container
     * @param AppService $service
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function add(Container $container, AppService $service)
    {

        // IF METHOD != POST ( IF FORM POST IS NOT SEND )
        if ($container->getRequest()->server->get('REQUEST_METHOD') != "POST") {
            $reponse = new Response($this->render('/back/Post/add.html.twig', [
                'active' => "articles",
                'myToken' => $this->getSession()->get('myToken'),
            ]));
            return $reponse->send();
        }

        // IF FORM IS SEND ( IF REQUEST == POST  // GET $POST
        $post = $container->getRequest()->request->all();
        $addPost = $service->getAddPost();

        if (!$lastId = $addPost->add($post)) {
            $response = new RedirectResponse($container->getAppServices()
                ->getLinkBuilder()
                ->getLink('PostAdminAdd'));
            return $response->send();
        }

        // REDIRECT TO POST/UPDATE/{article_id}
        $service->getFlash()->set('success', 'Votre article a bien été enregistré. Vous pouvez maintenant le modifier');
        $response = new RedirectResponse(
            $service->getLinkBuilder()->getLink('PostAdminUpdate', ['article_id' => $lastId])
        );
        return $response->send();
    }


    /**
     * @param AppService $service
     * @param Container $container
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function update(AppService $service, Container $container)
    {

        // GET ID POST AND ARTICLE
        $articleId = $container->getRequestParameters()->getParameters('article_id');
        $post = $container->getManager()->getPostManager()->read($articleId);

        // IF $POST DON'T EXIST
        if (!$post) {
            $service->getFlash()->set('warning', "L'article demandé n'existe pas");
            $response = new RedirectResponse($service->getLinkBuilder()->getLink('HomeAdmin'));
            return $response->send();
        }

        // ------------- IF METHOD = POST ( IF FORM POST IS SEND ) ---------
        if ($container->getRequest()->server->get('REQUEST_METHOD') == "POST") {
            // GET $FORM DATA
            $formData = $container->getRequest()->request->all();

            // WE CALL UPDATEPOST CLASS
            $updatePost = $service->getUpdatePost();
            $post = $updatePost->update($formData, $post);

            // IF ERROR
            if (!$post) {
                $response = new RedirectResponse($service->getLinkBuilder()->getLink('PostAdminUpdate', [
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

    /**
     * @param Container $container
     * @param AppService $service
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function delete(Container $container, AppService $service)
    {

        // GET POST DATA
        $formData = $this->getApp()->getRequest()->request->all();

        // CHECK IF TOKENS MATCH
        if ($formData['myToken'] != $this->getSession()->get('myToken')) {
            $service->getFlash()->set('warning', 'Erreur de token');
            $response = new RedirectResponse($service->getLinkBuilder()->getLink('PostAdminUpdate', [
                'article_id' => $formData['id_post'],
            ]));
            return $response->send();
        }

        // IF NO ERRORS WE DELETE THE POST

        // GET ID IMAGE
        $post = $container->getManager()->getPostManager()->read($formData['id_post']);
        $idImg = $post->getIdImage();

        // DELETE POST
        $manager = $container->getManager()->getPostManager()->delete($formData['id_post']);

        // DELETE IMG
        if ($idImg) {
            $deletePicture = new DeletePicture($this->getSession());
            $deletePicture->deleteImg($idImg);
        }

        // IF ERROR WE REDIRECT TO ADMIN UPDATE
        if (!$manager) {
            $service->getFlash()->set("warning", "Erreur lors de la supression de l'article");
            $response = new RedirectResponse($service->getLinkBuilder()->getLink('PostAdminUpdate', [
                'article_id' => $formData['id_post'],
            ]));
            return $response->send();
        }


        // IF NO ERRORS WE REDIRECT TO HOME ADMIN
        $service->getFlash()->set("success", "Votre article à bien été supprimé");
        $response = new RedirectResponse($service->getLinkBuilder()->getLink('HomeAdmin'));
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
