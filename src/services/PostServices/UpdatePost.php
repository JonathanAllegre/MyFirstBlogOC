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

    private function deleteImg($formData)
    {
        // IF IMG EXISTE
        $img = $this->manager->getPictureManager()->read($formData['deleteImg']);

        if (!$img) {
            $this->flash->set('warning', "Impossible de lire l'image");
            return false;
        }

        // IF DELETE IN BDD OK
        if (!$this->manager->getPictureManager()->delete($formData['deleteImg'])) {
            $this->flash->set('warning', "Une erreur est survenue pendant la suppression de l'image en base");
            return false;
        }

        $this->post->setIdImage(null);

        // CONSTRUCT URL
        $root = $this->app->getConfig()->getRootPath();
        $folder = substr($this->app->getConfig()->getImgBlogFolder(), 1);
        $nameImg = $img->getName();

        $url = $root . $folder . '/' . $nameImg;

        // DELETE FILE
        if (!unlink($url)) {
            $this->flash->set('warning', "Impossible de supprimer le fichier");
            return false;
        }

        $this->flash->set('success', "Votre image a bien été supprimée");
        return true;
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
            if (!$this->deleteImg($this->formData)) {
                return false;
            }
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
