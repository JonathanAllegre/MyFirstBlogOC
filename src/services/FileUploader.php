<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 08/04/2018
 * Time: 12:40
 */

namespace App\services;

use Symfony\Component\HttpFoundation\File\File;

class FileUploader
{
    private $targetDirectory;

    public function __construct(AppFactory $appFactory)
    {
        $rootPath = $appFactory->getConfig()->getRootPath();
        $this->targetDirectory = $rootPath.$appFactory->getConfig()->getImgBlogFolder();
    }

    public function upload(File $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
