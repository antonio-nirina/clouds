<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\SiteFormSetting;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="program")
 * @ORM\HasLifecycleCallbacks()
 */
class Program
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="is_multi_operation", type="boolean")
     */
    protected $is_multi_operation;

    /**
     * @ORM\Column(name="is_shopping",type="boolean")
     */
    protected $is_shopping;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\ProgramType")
     * @ORM\JoinColumn(nullable = false)
     */
    protected $type;

    /**
     * @ORM\Column(name="date_creation", type="datetime")
     */
    protected $date_creation;

    /**
     * @ORM\Column(name="date_launch", type="datetime")
     */
    protected $date_launch;

    /**
     * @ORM\Column(name="date_last_update", nullable=true, type="datetime")
     */
    protected $date_last_update;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumn(name="client_id")
     */
    protected $client;

    /**
     * @ORM\Column(name="url",type="string", length=255)
     */
    protected $url;

    /**
     * @ORM\Column(name="site_open",type="boolean")
     */
    protected $site_open;

    /**
     * @ORM\Column(name="status",type="boolean")
     *
     * en cours ou terminé
     */
    protected $status;

    /**
     * @ORM\Column(name="dotation_support",type="boolean")
     *
     * matérialisé ou dématérialisé
     */
    protected $dotation_support;
    
    /**
     * @ORM\Column(name="param_level", type="integer")
     *
     * level par rapport au 6 étapes de paramétrage
     */
    protected $param_level;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SiteFormSetting", mappedBy="program")
     */
    private $site_form_settings;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ProgramUser", mappedBy="program")
     */
    private $program_users;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Role", mappedBy="program")
     */
    private $roles;

    /**
     * @ORM\PrePersist
     */
    public function initProgram()
    {
        $this->param_level = 0;
        return $this->setDateCreation(new \DateTime);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->site_form_settings = new ArrayCollection();
    }

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
     * @return Program
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
     * Set isMultiOperation
     *
     * @param boolean $isMultiOperation
     *
     * @return Program
     */
    public function setIsMultiOperation($isMultiOperation)
    {
        $this->is_multi_operation = $isMultiOperation;

        return $this;
    }

    /**
     * Get isMultiOperation
     *
     * @return boolean
     */
    public function getIsMultiOperation()
    {
        return $this->is_multi_operation;
    }

    /**
     * Set isShopping
     *
     * @param boolean $isShopping
     *
     * @return Program
     */
    public function setIsShopping($isShopping)
    {
        $this->is_shopping = $isShopping;
        return $this;
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
     * Get isShopping
     *
     * @return boolean
     */
    public function getIsShopping()
    {
        return $this->is_shopping;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Program
     */
    public function setDateCreation($dateCreation)
    {
        $this->date_creation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * Set dateLaunch
     *
     * @param \DateTime $dateLaunch
     *
     * @return Program
     */
    public function setDateLaunch($dateLaunch)
    {
        $this->date_launch = $dateLaunch;

        return $this;
    }

    /**
     * Get dateLaunch
     *
     * @return \DateTime
     */
    public function getDateLaunch()
    {
        return $this->date_launch;
    }

    /**
     * Set dateLastUpdate
     *
     * @param \DateTime $dateLastUpdate
     *
     * @return Program
     */
    public function setDateLastUpdate($dateLastUpdate)
    {
        $this->date_last_update = $dateLastUpdate;

        return $this;
    }

    /**
     * Get dateLastUpdate
     *
     * @return \DateTime
     */
    public function getDateLastUpdate()
    {
        return $this->date_last_update;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Program
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set siteOpen
     *
     * @param boolean $siteOpen
     *
     * @return Program
     */
    public function setSiteOpen($siteOpen)
    {
        $this->site_open = $siteOpen;

        return $this;
    }

    /**
     * Get siteOpen
     *
     * @return boolean
     */
    public function getSiteOpen()
    {
        return $this->site_open;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Program
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dotationSupport
     *
     * @param boolean $dotationSupport
     *
     * @return Program
     */
    public function setDotationSupport($dotationSupport)
    {
        $this->dotation_support = $dotationSupport;

        return $this;
    }

    /**
     * Get dotationSupport
     *
     * @return boolean
     */
    public function getDotationSupport()
    {
        return $this->dotation_support;
    }

    /**
     * Set paramLevel
     *
     * @param integer $paramLevel
     *
     * @return Program
     */
    public function setParamLevel($paramLevel)
    {
        $this->param_level = $paramLevel;

        return $this;
    }

    /**
     * Get paramLevel
     *
     * @return integer
     */
    public function getParamLevel()
    {
        return $this->param_level;
    }

    /**
     * Set type
     *
     * @param \AdminBundle\Entity\ProgramType $type
     *
     * @return Program
     */
    public function setType(\AdminBundle\Entity\ProgramType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AdminBundle\Entity\ProgramType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Program
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
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

    /**
     * Add programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return Program
     */
    public function addProgramUser(\AdminBundle\Entity\ProgramUser $programUser)
    {
        $this->program_users[] = $programUser;

        return $this;
    }

    /**
     * Remove programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     */
    public function removeProgramUser(\AdminBundle\Entity\ProgramUser $programUser)
    {
        $this->program_users->removeElement($programUser);
    }

    /**
     * Get programUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgramUsers()
    {
        return $this->program_users;
    }

    /**
     * Add role
     *
     * @param \AdminBundle\Entity\Role $role
     *
     * @return Program
     */
    public function addRole(\AdminBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \AdminBundle\Entity\Role $role
     */
    public function removeRole(\AdminBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
