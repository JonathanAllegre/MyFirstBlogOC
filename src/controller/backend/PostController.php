<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 04/04/2018
 * Time: 19:04
 */

namespace App\controller\backend;

use App\controller\AppController;
use App\Entity\PostEntity;
use App\Manager\AppManager;
use App\services\AppFactory;
use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\RequestParameters;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends AppController
{
    public function add(
        AppManager $manager,
        Session $session,
        LinkBuilder $linkBuilder,
        CheckPermissions $checkPermissions,
        AppFactory $appFactory,
        Flash $flash
    ) {

        // IF USER IS NOT CONNECT OR IF USER DON'T HAVE PERMISION
        if (!$checkPermissions->isAdmin()) {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            return $response->send();
        }

        // IF METHOD != POST ( IF FORM POST IS NOT SEND )
        if ($appFactory->getRequest()->server->get('REQUEST_METHOD') != "POST") {
            $reponse = new Response($this->render('/back/Post/add.html.twig', [
                'active' => "articles",
                'myToken' => $session->get('myToken'),
            ]));
            return $reponse->send();
        }

        // IF FORM IS SEND ( IF REQUEST == POST )
        // GET $POST
        $post = $appFactory->getRequest()->request->all();

        // CHECK IF TOKENS MATCH
        if ($post['myToken'] != $session->get('myToken')) {
            $flash->set('warning', 'Erreur de token');
            $response = new RedirectResponse($linkBuilder->getLink('PostAdminAdd'));
            return $response->send();
        }

        // GET TIME
        $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

        // COMPLETE FOR CREATE ENTITY
        $post['created'] = $date->format('Y-m-d H:i:s');
        $post['modified'] = $date->format('Y-m-d H:i:s');
        $post['id_user'] = $session->get('user')['id'];

        // CREATE ENTITY
        $postEntity = new PostEntity($post);

        // PERSIST ENTITY
        $postManager = $manager->getPostManager();
        $postManager->create($postEntity);

        // GET LAST ID
        $lastId = $postManager->getLastId();

        // REDIRECT TO POST/UPDATE/{article_id}
        $flash->set('success', 'Votre article a bien été enregistré. Vous pouvez maintenant le modifier');
        $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
            'article_id' => $lastId
        ]));
        return $response->send();
    }

    public function update(
        AppFactory $appFactory,
        CheckPermissions $checkPermissions,
        LinkBuilder $linkBuilder,
        RequestParameters $parameters,
        AppManager $manager,
        Flash $flash,
        Session $session
    ) {

        // IF USER IS NOT CONNECT OR IF USER DON'T HAVE PERMISION
        if (!$checkPermissions->isAdmin()) {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            return $response->send();
        }

        // GET ID POST
        $articleId = $parameters->getParameters('article_id');

        // GET ARTICLE
        $post = $manager->getPostManager()->read($articleId);

        // IF POST DON'T EXIST
        if (!$post) {
            $flash->set('warning', "L'article demandé n'existe pas");
            $response = new RedirectResponse($linkBuilder->getLink('HomeAdmin'));
            return $response->send();
        }

        // IF METHOD = POST ( IF FORM POST IS SEND )
        if ($appFactory->getRequest()->server->get('REQUEST_METHOD') == "POST") {
            // GET TIME
            $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

            // GET $FORM DATA
            $formData = $appFactory->getRequest()->request->all();

            // CHECK IF TOKENS MATCH
            if ($formData['myToken'] != $session->get('myToken')) {
                $flash->set('warning', 'Erreur de token');
                $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
                    'article_id' => $articleId
                ]));
                return $response->send();
            }

            // UPDATE MODIFIED
            $formData['modified'] = $date->format('Y-m-d H:i:s');


            // UPDATE ENTITY
            $post->setTitle($formData['title']);
            $post->setShortText($formData['short_text']);
            $post->setContent($formData['content']);
            $post->setModified($formData['modified']);
            $post->setIdStatutPost($formData['id_statut_post']);

            // PERSIST
            if (!$manager->getPostManager()->update($post)) {
                $flash->set('warning', "Une erreur est survenue lors de l'enregistrment");
                $response = new RedirectResponse($linkBuilder->getLink('PostAdminUpdate', [
                    'article_id' => $articleId
                ]));
                return $response->send();
            }

            $flash->set('success', "Votre article a bien été sauvegardé");
            // READ NEW POST
            $post = $manager->getPostManager()->read($articleId);
        }



        $reponse = new Response($this->render('/back/Post/update.html.twig', [
                'active' => "articles",
                'post' => $post,
                'myToken' => $session->get('myToken'),
        ]));
        return $reponse->send();
    }
}
