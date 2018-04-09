<?php
namespace AdminBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FileExtensionConstraint extends Constraint
{
    public $message = 'Format de fichier incorrect';

    private $extension;

    public function __construct($extension)
    {
        $this->extension = $extension;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}
