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
use App\services\CheckPermissions;
use App\services\LinkBuilder;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostController extends AppController
{
    public function add(
        AppManager $manager,
        Session $session,
        LinkBuilder $linkBuilder,
        CheckPermissions $checkPermissions
    ) {

        // IF USER IS NOT CONNECT OR IF USER DON'T HAVE PERMISION
        if (!$checkPermissions->isAdmin()) {
            $response = new RedirectResponse($linkBuilder->getLink('Home'));
            return $response->send();
        }


        // CREATE ENTITY
        $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));
        $post = new PostEntity(array(
            'created' => $date->format('Y-m-d H:i:s'),
            'modified' => $date->format('Y-m-d H:i:s'),
            'title' => "Un Titre",
            'short_text' => "Un texte court",
            'content' => "Le contenu",
            'id_user' => $session->get('user')['id'],
            'id_statut_post' => 2,
        ));

        // PERSIST ENTITY
        $postManager = $manager->getPostManager();
        $postManager->create($post);
        return var_dump($post);
    }
}
