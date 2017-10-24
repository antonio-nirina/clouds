<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\Program;

/**
 * @ORM\Entity
 * @ORM\Table(name="site_form_setting")
 */
class SiteFormSetting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="site_form_settings")
     */
    private $program;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validation;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notification;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $import;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $export;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $header_image;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $site_form_text;


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
     * Set state
     *
     * @param boolean $state
     *
     * @return SiteFormSetting
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return boolean
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set validation
     *
     * @param boolean $validation
     *
     * @return SiteFormSetting
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * Get validation
     *
     * @return boolean
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Set notification
     *
     * @param boolean $notification
     *
     * @return SiteFormSetting
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return boolean
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set import
     *
     * @param boolean $import
     *
     * @return SiteFormSetting
     */
    public function setImport($import)
    {
        $this->import = $import;

        return $this;
    }

    /**
     * Get import
     *
     * @return boolean
     */
    public function getImport()
    {
        return $this->import;
    }

    /**
     * Set export
     *
     * @param boolean $export
     *
     * @return SiteFormSetting
     */
    public function setExport($export)
    {
        $this->export = $export;

        return $this;
    }

    /**
     * Get export
     *
     * @return boolean
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Set headerImage
     *
     * @param string $headerImage
     *
     * @return SiteFormSetting
     */
    public function setHeaderImage($headerImage)
    {
        $this->header_image = $headerImage;

        return $this;
    }

    /**
     * Get headerImage
     *
     * @return string
     */
    public function getHeaderImage()
    {
        return $this->header_image;
    }

    /**
     * Set siteFormText
     *
     * @param string $siteFormText
     *
     * @return SiteFormSetting
     */
    public function setSiteFormText($siteFormText)
    {
        $this->site_form_text = $siteFormText;

        return $this;
    }

    /**
     * Get siteFormText
     *
     * @return string
     */
    public function getSiteFormText()
    {
        return $this->site_form_text;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return SiteFormSetting
     */
    public function setProgram(Program $program = null)
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
