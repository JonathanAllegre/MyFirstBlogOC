<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 19/04/2018
 * Time: 16:49
 */

namespace App\services\PictureServices;

use App\Manager\AppManager;
use App\services\AppFactory;
use App\services\FileUploader;
use App\services\Sessions\Flash;
use Symfony\Component\HttpFoundation\Session\Session;

class DeletePicture
{
    private $app;
    private $manager;
    private $flash;
    private $fileUploader;
    private $session;


    public function __construct(Session $session)
    {
        $this->app = new AppFactory();
        $this->manager = new AppManager($this->app);
        $this->flash = new Flash($session);
        $this->fileUploader = new FileUploader($this->app);

        $this->session = $session;
    }

    public function deleteImg($idImg)
    {
        // IF IMG EXISTE
        $img = $this->manager->getPictureManager()->read($idImg);

        if (!$img) {
            $this->flash->set('warning', "Impossible de lire l'image");
            return false;
        }

        // IF DELETE IN BDD OK
        if (!$this->manager->getPictureManager()->delete($idImg)) {
            $this->flash->set('warning', "Une erreur est survenue pendant la suppression de l'image en base");
            return false;
        }


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
}
