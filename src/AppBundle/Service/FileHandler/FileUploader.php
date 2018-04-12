<?php

namespace AppBundle\Service\FileHandler;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file, $id = false)
    {
        // $file_name = md5(uniqid()).'.'.$file->guessExtension();
        $fileName = $file->getClientOriginalName();

        if ($id) {
            $file->move($this->getTargetDir() . "/$id", $fileName);
        } else {
            $file->move($this->getTargetDir(), $fileName);
        }

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function getFile($fileName, $id = false)
    {
        if ($id) {
            $file = new File($this->getTargetDir() . "/$id" . "/$fileName");
        } else {
            $file = new File($this->getTargetDir() . "/$fileName");
        }

        return $file;
    }
}
