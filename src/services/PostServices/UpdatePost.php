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
    private $formData;
    private $session;
    private $post;

    public function __construct($formData, Session $session, PostEntity $post)
    {
        $this->app = new AppFactory();
        $this->manager = new AppManager($this->app);
        $this->flash = new Flash($session);
        $this->fileUploader = new FileUploader($this->app);

        $this->formData = $formData;
        $this->session = $session;
        $this->post = $post;
    }


    public function update()
    {

        // GET TIME
        $date = new \DateTime(null, new \DateTimeZone('Europe/Paris'));

        // CHECK IF TOKENS MATCH
        if ($this->formData['myToken'] != $this->session->get('myToken')) {
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
                    $this->post->setIdImage($this->manager->getPictureManager()->getLastId());
                }
            }
        }

        // IF DELETE IMG CHECKED
        if (isset($this->formData['deleteImg'])) {
            //NEW DELETEPICTURE
            $deletePicture = new DeletePicture($this->session, $this->post);

            //IF ERROR RETURN FALSE
            if (!$deletePicture->deleteImg($this->formData['deleteImg'])) {
                return false;
            }

            // UPDATE ENTITY
            $this->post->setIdImage(null);
        }

        // UPDATE ENTITY
        $this->post->setTitle($this->formData['title']);
        $this->post->setShortText($this->formData['short_text']);
        $this->post->setContent($this->formData['content']);
        $this->post->setModified($date->format('Y-m-d H:i:s'));
        $this->post->setIdStatutPost($this->formData['id_statut_post']);

        // IF ERROR IN PERSIST
        if (!$this->manager->getPostManager()->update($this->post)) {
            $this->flash->set('warning', "Une erreur est survenue lors de l'enregistrement");
            return false;
        }

        // IF NO ERROR WE RETURN THE NEW OBJECT
        $this->flash->set('success', "Votre article a bien été sauvegardé");
        $post = $this->manager->getPostManager()->read($this->formData['id_post']);
        return $post;
    }
}
