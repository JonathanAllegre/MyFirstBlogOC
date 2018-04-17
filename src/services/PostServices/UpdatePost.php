<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/04/2018
 * Time: 17:44
 */

namespace App\services\PostServices;

use App\Entity\PictureEntity;
use App\Entity\PostEntity;
use App\Manager\AppManager;
use App\services\AppFactory;
use App\services\FileUploader;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Session\Session;

class UpdatePost
{
    public function update($formData, Session $session, PostEntity $post)
    {
        $app = new AppFactory();
        $manager = new AppManager($app);
        $flash = new Flash($session);
        $fileUploader = new FileUploader($app);

        // GET TIME
        $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

        // CHECK IF TOKENS MATCH
        if ($formData['myToken'] != $session->get('myToken')) {
            $flash->set('warning', 'Erreur de token');
            return false;
        }

        // IF IMAGE IS SEND
        $image = $app->getRequest()->files->get('file');
        if ($image) {
            $name = $fileUploader->upload($image);

            // IF SUCCESS UPLOAD WE PERSIST FILE
            if ($name) {
                $data = new PictureEntity([
                    'created' => $date->format('Y-m-d H:i:s'),
                    'name' => $name,
                ]);
                // PERSIST FILE
                if ($manager->getPictureManager()->create($data)) {
                    $flash->set('success', "Votre image a bien été envoyé");
                    $post->setIdImage($manager->getPictureManager()->getLastId());
                }
            }
        }

        // UPDATE ENTITY
        $post->setTitle($formData['title']);
        $post->setShortText($formData['short_text']);
        $post->setContent($formData['content']);
        $post->setModified($date->format('Y-m-d H:i:s'));
        $post->setIdStatutPost($formData['id_statut_post']);

        // IF ERROR IN PERSIST
        if (!$manager->getPostManager()->update($post)) {
            $flash->set('warning', "Une erreur est survenue lors de l'enregistrement");
            return false;
        }

        // IF NO ERROR WE RETURN THE NEW OBJECT
        $flash->set('success', "Votre article a bien été sauvegardé");
        $post = $manager->getPostManager()->read($formData['id_post']);
        return $post;
    }
}
