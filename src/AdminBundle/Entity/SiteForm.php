<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="site_form")
 */
class SiteForm
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $form_type;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\SiteFormSetting", mappedBy="site_form")
     */
    private $site_form_setting;

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
     * Set name
     *
     * @param string $name
     *
     * @return SiteForm
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set formType
     *
     * @param string $formType
     *
     * @return SiteForm
     */
    public function setFormType($formType)
    {
        $this->form_type = $formType;

        return $this;
    }

    /**
     * Get formType
     *
     * @return string
     */
    public function getFormType()
    {
        return $this->form_type;
    }

    /**
     * Set siteFormSetting
     *
     * @param \AdminBundle\Entity\SiteFormSetting $siteFormSetting
     *
     * @return SiteForm
     */
    public function setSiteFormSetting(\AdminBundle\Entity\SiteFormSetting $siteFormSetting = null)
    {
        $this->site_form_setting = $siteFormSetting;

        return $this;
    }

    /**
     * Get siteFormSetting
     *
     * @return \AdminBundle\Entity\SiteFormSetting
     */
    public function getSiteFormSetting()
    {
        return $this->site_form_setting;
    }
}
