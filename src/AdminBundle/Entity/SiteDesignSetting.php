<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="site_design_setting")
 */
class SiteDesignSetting
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name_logo", type="string", length=255, nullable=true)
     */
    private $logo_name;

    /**
     * @ORM\Column(name="logo_path", type="string", length=255, nullable=true)
     * @Assert\Image(
     *     maxSize = "8M",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"}
     * )
     */
    private $logo_path;

    /**
     * @ORM\Column(name="colors", type="array", nullable=true)
     */
    private $colors;

    /**
     * @ORM\Column(name="police", type="string", length=255, nullable=true)
     */
    private $police;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program")
     */
    private $program;

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
     * Set logoName
     *
     * @param string $logoName
     *
     * @return SiteDesignSetting
     */
    public function setLogoName($logoName)
    {
        $this->logo_name = $logoName;

        return $this;
    }

    /**
     * Get logoName
     *
     * @return string
     */
    public function getLogoName()
    {
        return $this->logo_name;
    }

    /**
     * Set logoPath
     *
     * @param string $logoPath
     *
     * @return SiteDesignSetting
     */
    public function setLogoPath($logoPath)
    {
        $this->logo_path = $logoPath;

        return $this;
    }

    /**
     * Get logoPath
     *
     * @return string
     */
    public function getLogoPath()
    {
        return $this->logo_path;
    }

    /**
     * Set colors
     *
     * @param array $colors
     *
     * @return SiteDesignSetting
     */
    public function setColors($colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * Get colors
     *
     * @return array
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * Set police
     *
     * @param string $police
     *
     * @return SiteDesignSetting
     */
    public function setPolice($police)
    {
        $this->police = $police;

        return $this;
    }

    /**
     * Get police
     *
     * @return string
     */
    public function getPolice()
    {
        return $this->police;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\AdminBundle:Program $program
     *
     * @return SiteDesignSetting
     */
    

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return SiteDesignSetting
     */
    public function setProgram(\AdminBundle\Entity\Program $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return \AdminBundle\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }
}