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
use App\services\PictureServices\DeletePicture;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Session\Session;

class UpdatePost
{
    private $app;
    private $manager;
    private $flash;
    private $fileUploader;
    private $session;

    public function __construct(
        Session $session,
        AppManager $manager,
        Flash $flash,
        AppFactory $app,
        FileUploader $fileUploader
    ) {
        $this->app = $app;
        $this->manager = $manager;
        $this->flash = $flash;
        $this->fileUploader = $fileUploader;
        $this->session = $session;
    }


    public function update($formData, PostEntity $post)
    {

        // GET TIME
        $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

        // CHECK IF TOKENS MATCH
        if ($formData['myToken'] != $this->session->get('myToken')) {
            $this->flash->set('warning', 'Erreur de token');
            return false;
        }

        // IF IMAGE IS SEND
        $image = $this->app->getRequest()->files->get('file');
        if ($image) {
            $name = $this->fileUploader->upload($image);

            // IF SUCCESS UPLOAD WE PERSIST FILE
            if ($name) {
                $data = new PictureEntity([
                    'created' => $date->format('Y-m-d H:i:s'),
                    'name' => $name,
                ]);
                // PERSIST FILE
                if ($this->manager->getPictureManager()->create($data)) {
                    $this->flash->set('success', "Votre image a bien été envoyé");
                    $post->setIdImage($this->manager->getPictureManager()->getLastId());
                }
            }
        }

        // IF DELETE IMG CHECKED
        if (isset($formData['deleteImg'])) {
            //NEW DELETEPICTURE
            $deletePicture = new DeletePicture($this->session, $post);

            //IF ERROR RETURN FALSE
            if (!$deletePicture->deleteImg($formData['deleteImg'])) {
                return false;
            }

            // UPDATE ENTITY
            $post->setIdImage(null);
        }

        // UPDATE ENTITY
        $post->setTitle($formData['title']);
        $post->setShortText($formData['short_text']);
        $post->setContent($formData['content']);
        $post->setModified($date->format('Y-m-d H:i:s'));
        $post->setIdStatutPost($formData['id_statut_post']);

        // IF ERROR IN PERSIST
        if (!$this->manager->getPostManager()->update($post)) {
            $this->flash->set('warning', "Une erreur est survenue lors de l'enregistrement");
            return false;
        }

        // IF NO ERROR WE RETURN THE NEW OBJECT
        $this->flash->set('success', "Votre article a bien été sauvegardé");
        $post = $this->manager->getPostManager()->read($formData['id_post']);
        return $post;
    }
}
