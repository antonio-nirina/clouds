<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\Program;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SiteFormSettingRepository")
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
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\SiteForm", inversedBy="site_form_setting")
     */
    private $site_form;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SiteFormFieldSetting", mappedBy="site_form_setting")
     */
    private $site_form_field_settings;


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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->site_form_field_settings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add siteFormFieldSetting
     *
     * @param \AdminBundle\Entity\SiteFormFieldSetting $siteFormFieldSetting
     *
     * @return SiteFormSetting
     */
    public function addSiteFormFieldSetting(\AdminBundle\Entity\SiteFormFieldSetting $siteFormFieldSetting)
    {
        $this->site_form_field_settings[] = $siteFormFieldSetting;

        return $this;
    }

    /**
     * Remove siteFormFieldSetting
     *
     * @param \AdminBundle\Entity\SiteFormFieldSetting $siteFormFieldSetting
     */
    public function removeSiteFormFieldSetting(\AdminBundle\Entity\SiteFormFieldSetting $siteFormFieldSetting)
    {
        $this->site_form_field_settings->removeElement($siteFormFieldSetting);
    }

    /**
     * Get siteFormFieldSettings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiteFormFieldSettings()
    {
        return $this->site_form_field_settings;
    }

    /**
     * Set siteForm
     *
     * @param \AdminBundle\Entity\SiteForm $siteForm
     *
     * @return SiteFormSetting
     */
    public function setSiteForm(\AdminBundle\Entity\SiteForm $siteForm = null)
    {
        $this->site_form = $siteForm;

        return $this;
    }

    /**
     * Get siteForm
     *
     * @return \AdminBundle\Entity\SiteForm
     */
    public function getSiteForm()
    {
        return $this->site_form;
    }
}
