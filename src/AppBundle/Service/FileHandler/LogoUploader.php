<?php

namespace AppBundle\Service\FileHandler;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class LogoUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file, $id = false)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        if ($id) {
            $file->move($this->getTargetDir()."/$id", $fileName);
        } else {
            $file->move($this->getTargetDir(), $fileName);
        }

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
}
