<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SitePagesStandardSettingRepository")
 * @ORM\Table(name="site_pages_standard_setting")
 */
class SitePagesStandardSetting
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nom_page;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    private $titre_page;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    private $menu_page;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    private $img_page;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    private $contenu_page;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="site_page_standard")
     */
    private $program;
	
	/**
     * @ORM\Column(type="boolean")
     */
    private $status_page;


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
     * Set statusPage
     *
     * @param boolean $statusPage
     *
     * @return SitePagesStandardSetting
     */
    public function setStatusPage($statusPage)
    {
        $this->status_page = $statusPage;

        return $this;
    }

    /**
     * Get statusPage
     *
     * @return boolean
     */
    public function getStatusPage()
    {
        return $this->status_page;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return SitePagesStandardSetting
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

    /**
     * Set nomPage
     *
     * @param string $nomPage
     *
     * @return SitePagesStandardSetting
     */
    public function setNomPage($nomPage)
    {
        $this->nom_page = $nomPage;

        return $this;
    }

    /**
     * Get nomPage
     *
     * @return string
     */
    public function getNomPage()
    {
        return $this->nom_page;
    }

    /**
     * Set titrePage
     *
     * @param string $titrePage
     *
     * @return SitePagesStandardSetting
     */
    public function setTitrePage($titrePage)
    {
        $this->titre_page = $titrePage;

        return $this;
    }

    /**
     * Get titrePage
     *
     * @return string
     */
    public function getTitrePage()
    {
        return $this->titre_page;
    }

    /**
     * Set menuPage
     *
     * @param string $menuPage
     *
     * @return SitePagesStandardSetting
     */
    public function setMenuPage($menuPage)
    {
        $this->menu_page = $menuPage;

        return $this;
    }

    /**
     * Get menuPage
     *
     * @return string
     */
    public function getMenuPage()
    {
        return $this->menu_page;
    }

    /**
     * Set imgPage
     *
     * @param string $imgPage
     *
     * @return SitePagesStandardSetting
     */
    public function setImgPage($imgPage)
    {
        $this->img_page = $imgPage;

        return $this;
    }

    /**
     * Get imgPage
     *
     * @return string
     */
    public function getImgPage()
    {
        return $this->img_page;
    }

    /**
     * Set contenuPage
     *
     * @param string $contenuPage
     *
     * @return SitePagesStandardSetting
     */
    public function setContenuPage($contenuPage)
    {
        $this->contenu_page = $contenuPage;

        return $this;
    }

    /**
     * Get contenuPage
     *
     * @return string
     */
    public function getContenuPage()
    {
        return $this->contenu_page;
    }
}
