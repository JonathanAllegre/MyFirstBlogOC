<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 08/04/2018
 * Time: 12:40
 */

namespace App\services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;
    private $legalExtensions = array("JPEG", "PNG", "GIF");
    private $legalSize = 5000000;


    public function __construct(AppFactory $appFactory)
    {
        $rootPath = $appFactory->getConfig()->getRootPath();
        $this->targetDirectory = $rootPath.$appFactory->getConfig()->getImgBlogFolder();
    }

    public function upload(UploadedFile $file)
    {

        // CHECK SIZE FILE
        if ($this->legalSize > $file->getClientSize()) {
            // CHECK EXTENSION
            $extension = strtoupper($file->guessExtension());
            if (in_array($extension, $this->legalExtensions)) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getTargetDirectory(), $fileName);
                return $fileName;
            }
        }

        return false;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
