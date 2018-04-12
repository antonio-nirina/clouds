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
    protected $duplicationSourceId;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setDuplicationSourceId($id)
    {
        $this->duplicationSourceId = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuplicationSourceId()
    {
        return $this->duplicationSourceId;
    }
}
