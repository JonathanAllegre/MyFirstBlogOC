<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/04/2018
 * Time: 17:05
 */

namespace App\services\PostServices;

use App\Entity\PostEntity;
use App\Manager\AppManager;
use App\services\AppFactory;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Session\Session;

class AddPost
{
    public function add(Session $session, $post)
    {
        $app = new AppFactory();
        $manager = new AppManager($app);
        $flash = new Flash($session);

        // CHECK IF TOKENS MATCH
        if ($post['myToken'] != $session->get('myToken')) {
            $flash->set('warning', 'Erreur de token');
            return false;
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

        return $lastId;
    }
}
