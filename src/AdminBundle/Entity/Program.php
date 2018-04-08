<?php
namespace AdminBundle\Entity;

use AdminBundle\Entity\SiteDesignSetting;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Entity\SiteTableNetworkSetting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ProgramRepository")
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
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Role", mappedBy="program", cascade={"persist"})
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\PeriodPointSetting", mappedBy="program", cascade={"persist"})
     */
    private $period_point_setting;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\RegistrationFormData", inversedBy="program")
     */
    private $registration_form_data;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\LoginPortalData", inversedBy="program")
     */
    private $login_portal_data;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\HomePageData", inversedBy="program")
     */
    private $home_page_data;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\SiteDesignSetting", mappedBy="program")
     */
    private $site_design_setting;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\SiteTableNetworkSetting", mappedBy="program")
     */
    private $site_table_network_setting;
    
    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SitePagesStandardSetting", mappedBy="program")
     */
    private $site_page_standard;
    
    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuiz", mappedBy="program")
     */
    private $sondages_quiz;
    
    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ResultatsSondagesQuiz", mappedBy="program")
     */
    private $resultats_sondages_quiz;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\PointAttributionSetting", mappedBy="program")
     */
    private $point_attribution_setting;
    
    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\HomePagePost", mappedBy="program")
     */
    private $home_page_post;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearning", mappedBy="program")
     */
    private $e_learnings;

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
        $this->site_page_standard = new ArrayCollection();
        $this->sondages_quiz = new ArrayCollection();
        $this->resultats_sondages_quiz = new ArrayCollection();
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

    /**
     * Set registrationFormData
     *
     * @param \AdminBundle\Entity\RegistrationFormData $registrationFormData
     *
     * @return Program
     */
    public function setRegistrationFormData(\AdminBundle\Entity\RegistrationFormData $registrationFormData = null)
    {
        $this->registration_form_data = $registrationFormData;

        return $this;
    }

    /**
     * Get registrationFormData
     *
     * @return \AdminBundle\Entity\RegistrationFormData
     */
    public function getRegistrationFormData()
    {
        return $this->registration_form_data;
    }

    /**
     * Set loginPortalData
     *
     * @param \AdminBundle\Entity\LoginPortalData $loginPortalData
     *
     * @return Program
     */
    public function setLoginPortalData(\AdminBundle\Entity\LoginPortalData $loginPortalData = null)
    {
        $this->login_portal_data = $loginPortalData;

        return $this;
    }

    /**
     * Get loginPortalData
     *
     * @return \AdminBundle\Entity\LoginPortalData
     */
    public function getLoginPortalData()
    {
        return $this->login_portal_data;
    }

    /**
     * Set homePageData
     *
     * @param \AdminBundle\Entity\HomePageData $homePageData
     *
     * @return Program
     */
    public function setHomePageData(\AdminBundle\Entity\HomePageData $homePageData = null)
    {
        $this->home_page_data = $homePageData;

        return $this;
    }

    /**
     * Get homePageData
     *
     * @return \AdminBundle\Entity\HomePageData
     */
    public function getHomePageData()
    {
        return $this->home_page_data;
    }

    /**
     * Set siteTableNetworkSetting
     *
     * @param \AdminBundle\Entity\SiteTableNetworkSetting $siteTableNetworkSetting
     *
     * @return Program
     */
    public function setSiteTableNetworkSetting(SiteTableNetworkSetting $siteTableNetworkSetting = null)
    {
        $this->site_table_network_setting = $siteTableNetworkSetting;

        return $this;
    }

    /**
     * Get siteTableNetworkSetting
     *
     * @return \AdminBundle\Entity\SiteTableNetworkSetting
     */
    public function getSiteTableNetworkSetting()
    {
        return $this->site_table_network_setting;
    }

    /**
     * Set siteDesignSetting
     *
     * @param \AdminBundle\Entity\SiteDesignSetting $siteDesignSetting
     *
     * @return Program
     */
    public function setSiteDesignSetting(SiteDesignSetting $siteDesignSetting = null)
    {
        $this->site_design_setting = $siteDesignSetting;

        return $this;
    }

    /**
     * Get siteDesignSetting
     *
     * @return \AdminBundle\Entity\SiteDesignSetting
     */
    public function getSiteDesignSetting()
    {
        return $this->site_design_setting;
    }
    
    /**
     * Add sitePagesStandardSetting
     *
     * @param \AdminBundle\Entity\SitePagesStandardSetting $sitePagesStandardSetting
     *
     * @return SitePagesStandardSetting
     */
    public function addSitePagesStandardSetting(\AdminBundle\Entity\SitePagesStandardSetting $sitePagesStandardSetting)
    {
        $this->site_page_standard[] = $sitePagesStandardSetting;

        return $this;
    }

    /**
     * Remove sitePagesStandardSetting
     *
     * @param \AdminBundle\Entity\SitePagesStandardSetting $sitePagesStandardSetting
     */
    public function removeSitePagesStandardSetting(\AdminBundle\Entity\SitePagesStandardSetting $sitePagesStandardSetting)
    {
        $this->site_page_standard->removeElement($sitePagesStandardSetting);
    }

    /**
     * Get sitePagesStandardSetting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSitePagesStandardSetting()
    {
        return $this->site_page_standard;
    }

    /**
     * Add periodPointSetting
     *
     * @param \AdminBundle\Entity\PeriodPointSetting $periodPointSetting
     *
     * @return Program
     */
    public function addPeriodPointSetting(\AdminBundle\Entity\PeriodPointSetting $periodPointSetting)
    {
        $this->period_point_setting[] = $periodPointSetting;

        return $this;
    }

    /**
     * Remove periodPointSetting
     *
     * @param \AdminBundle\Entity\PeriodPointSetting $periodPointSetting
     */
    public function removePeriodPointSetting(\AdminBundle\Entity\PeriodPointSetting $periodPointSetting)
    {
        $this->period_point_setting->removeElement($periodPointSetting);
    }

    /**
     * Get periodPointSetting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriodPointSetting()
    {
        return $this->period_point_setting;
    }

    /**
     * Add sitePageStandard
     *
     * @param \AdminBundle\Entity\SitePagesStandardSetting $sitePageStandard
     *
     * @return Program
     */
    public function addSitePageStandard(\AdminBundle\Entity\SitePagesStandardSetting $sitePageStandard)
    {
        $this->site_page_standard[] = $sitePageStandard;

        return $this;
    }

    /**
     * Remove sitePageStandard
     *
     * @param \AdminBundle\Entity\SitePagesStandardSetting $sitePageStandard
     */
    public function removeSitePageStandard(\AdminBundle\Entity\SitePagesStandardSetting $sitePageStandard)
    {
        $this->site_page_standard->removeElement($sitePageStandard);
    }

    /**
     * Get sitePageStandard
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSitePageStandard()
    {
        return $this->site_page_standard;
    }
    

    /**
     * Add pointAttributionSetting
     *
     * @param \AdminBundle\Entity\PointAttributionSetting $pointAttributionSetting
     *
     * @return Program
     */
    public function addPointAttributionSetting(\AdminBundle\Entity\PointAttributionSetting $pointAttributionSetting)
    {
        $this->point_attribution_setting[] = $pointAttributionSetting;

        return $this;
    }

    /**
     * Remove pointAttributionSetting
     *
     * @param \AdminBundle\Entity\PointAttributionSetting $pointAttributionSetting
     */
    public function removePointAttributionSetting(\AdminBundle\Entity\PointAttributionSetting $pointAttributionSetting)
    {
        $this->point_attribution_setting->removeElement($pointAttributionSetting);
    }

    /**
     * Get pointAttributionSetting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPointAttributionSetting()
    {
        return $this->point_attribution_setting;
    }

    /**
     * Add homePagePost
     *
     * @param \AdminBundle\Entity\HomePagePost $homePagePost
     *
     * @return Program
     */
    public function addHomePagePost(\AdminBundle\Entity\HomePagePost $homePagePost)
    {
        $this->home_page_post[] = $homePagePost;

        return $this;
    }

    /**
     * Remove homePagePost
     *
     * @param \AdminBundle\Entity\HomePagePost $homePagePost
     */
    public function removeHomePagePost(\AdminBundle\Entity\HomePagePost $homePagePost)
    {
        $this->home_page_post->removeElement($homePagePost);
    }

    /**
     * Get homePagePost
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHomePagePost()
    {
        return $this->home_page_post;
    }
    

    /**
     * Add sondagesQuiz
     *
     * @param \AdminBundle\Entity\SondagesQuiz $sondagesQuiz
     *
     * @return SondagesQuiz
     */
    public function addSondagesQuiz(\AdminBundle\Entity\SondagesQuiz $sondagesQuiz)
    {
        $this->sondages_quiz[] = $sondagesQuiz;

        return $this;
    }

    /**
     * Remove sondagesQuiz
     *
     * @param \AdminBundle\Entity\SondagesQuiz $sondagesQuiz
     */
    public function removeSondagesQuiz(\AdminBundle\Entity\SondagesQuiz $sondagesQuiz)
    {
        $this->sondages_quiz->removeElement($sondagesQuiz);
    }

    /**
     * Get sondagesQuiz
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSondagesQuiz()
    {
        return $this->sondages_quiz;
    }
    
    
    
    /**
     * Add resultatsSondagesQuiz
     *
     * @param \AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz
     *
     * @return ResultatsSondagesQuiz
     */
    public function addResultatsSondagesQuiz(\AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz)
    {
        $this->resultats_sondages_quiz[] = $resultatsSondagesQuiz;

        return $this;
    }

    /**
     * Remove resultatsSondagesQuiz
     *
     * @param \AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz
     */
    public function removeResultatsSondagesQuiz(\AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz)
    {
        $this->resultats_sondages_quiz->removeElement($resultatsSondagesQuiz);
    }

    /**
     * Get resultatsSondagesQuiz
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultatsSondagesQuiz()
    {
        return $this->resultats_sondages_quiz;
    }

    /**
     * Add eLearning
     *
     * @param \AdminBundle\Entity\ELearning $eLearning
     *
     * @return Program
     */
    public function addELearning(\AdminBundle\Entity\ELearning $eLearning)
    {
        $this->e_learnings[] = $eLearning;

        return $this;
    }

    /**
     * Remove eLearning
     *
     * @param \AdminBundle\Entity\ELearning $eLearning
     */
    public function removeELearning(\AdminBundle\Entity\ELearning $eLearning)
    {
        $this->e_learnings->removeElement($eLearning);
    }

    /**
     * Get eLearnings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getELearnings()
    {
        return $this->e_learnings;
    }
}
