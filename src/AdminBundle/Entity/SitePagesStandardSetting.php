<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
	
	/**
     * @Assert\File(maxSize="6000000")
     */
    private $img_page;


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
	
	/**
     * Sets img_page.
     *
     * @param UploadedFile $img_page
     */
    public function setImgPage(UploadedFile $img_page = null)
    {
        $this->img_page = $img_page;
    }

    /**
     * Get img_page.
     *
     * @return UploadedFile
     */
    public function getImgPage()
    {
        return $this->img_page;
    }
	
	
	public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'pages_standards';
    }
	
	public function upload(\AdminBundle\Entity\Program $program = null)
	{
		// the file property can be empty if the field is not required
		if (null === $this->getImgPage()) {
			return;
		}

		// use the original file name here but you should
		// sanitize it at least to avoid any security issues

		// move takes the target directory and then the
		// target filename to move to
		$this->getImgPage()->move(
			$this->getUploadRootDir().'/'.$program->getId(),
			$this->getImgPage()->getClientOriginalName()
		);

		// set the path property to the filename where you've saved the file
		$this->path = $this->getImgPage()->getClientOriginalName();

		// clean up the file property as you won't need it anymore
		$this->img_page = null;
	}
	
	/**
     * Set Path
     *
     * @param string $path
     *
     * @return SitePagesStandardSetting
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
	
	/**
     * Get path.
     *
     * @return path
     */
    public function getPath()
    {
        return $this->path;
    }
}
