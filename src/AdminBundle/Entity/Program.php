<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SiteFormSetting;

/**
 * @ORM\Entity
 * @ORM\Table(name="program")
 */
class Program
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SiteFormSetting", mappedBy="program")
     */
    private $site_form_settings;

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
     * Constructor
     */
    public function __construct()
    {
        $this->site_form_settings = new ArrayCollection();
    }

    /**
     * Add siteFormSetting
     *
     * @param \AdminBundle\Entity\SiteFormSetting $siteFormSetting
     *
     * @return Program
     */
    public function addSiteFormSetting(SiteFormSetting $siteFormSetting)
    {
        $this->site_form_settings[] = $siteFormSetting;

        return $this;
    }

    /**
     * Remove siteFormSetting
     *
     * @param \AdminBundle\Entity\SiteFormSetting $siteFormSetting
     */
    public function removeSiteFormSetting(SiteFormSetting $siteFormSetting)
    {
        $this->site_form_settings->removeElement($siteFormSetting);
    }

    /**
     * Get siteFormSettings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiteFormSettings()
    {
        return $this->site_form_settings;
    }
}
