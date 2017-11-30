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
        $file_name = $file->getClientOriginalName();

        if ($id) {
            $file->move($this->getTargetDir()."/$id", $file_name);
        } else {
            $file->move($this->getTargetDir(), $file_name);
        }

        return $file_name;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function getFile($file_name, $id = false)
    {
        if ($id) {
            $file = new File($this->getTargetDir()."/$id"."/$file_name");
        } else {
            $file = new File($this->getTargetDir()."/$file_name");
        }

        return $file;
    }
}
