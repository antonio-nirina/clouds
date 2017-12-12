<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\PointAttributionTypeRepository")
 * @ORM\Table(name="point_attribution_type")
 */
class PointAttributionType
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="point_type_name", type="string", length=255)
     */
    private $point_type_name;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;

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
     * Set pointTypeName
     *
     * @param string $pointTypeName
     *
     * @return PointAttributionType
     */
    public function setPointTypeName($pointTypeName)
    {
        $this->point_type_name = $pointTypeName;

        return $this;
    }

    /**
     * Get pointTypeName
     *
     * @return string
     */
    public function getPointTypeName()
    {
        return $this->point_type_name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PointAttributionType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
