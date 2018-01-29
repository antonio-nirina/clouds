<?php
namespace AdminBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DuplicationData
{
    const ERROR_MESSAGE_NAME_ALREADY_USED = 'Cette valeur est déjà utilisée.';

    /**
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     */
    protected $duplication_source_id;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDuplicationSourceId($id)
    {
        $this->duplication_source_id = $id;

        return $this;
    }

    public function getDuplicationSourceId()
    {
        return $this->duplication_source_id;
    }
}