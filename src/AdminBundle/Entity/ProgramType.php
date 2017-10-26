<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="program_type")
 */
class ProgramType
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;
   
    /**
     * @ORM\Column(name="presentation", type="text")
     */
    protected $presentation;

    /**
     * @ORM\Column(name="image_url", type="string", length=255)
     */
    protected $image_url;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ProgramType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     *
     * @return ProgramType
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return ProgramType
     */
    public function setImageUrl($imageUrl)
    {
        $this->image_url = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }
}
