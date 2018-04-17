<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $contenu_page;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="site_page_standard")
     */
    private $program;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status_page = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $img_page;

    /**
     * @Gedmo\Slug(fields={"menu_page"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="array")
     */
    private $options;

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
     * Set options
     *
     * @param boolean $options
     *
     * @return SitePagesStandardSetting
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
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
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    public function getUploadRootDir()
    {
        global $kernel;
        return $kernel->getProjectDir().'/web/content/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'pages_standards';
    }

    public function upload(\AdminBundle\Entity\Program $program = null)
    {
        if (null === $this->getImgPage()) {
            return;
        }

        $this->getImgPage()->move(
            $this->getUploadRootDir() . '/' . $program->getId(),
            $this->getImgPage()->getClientOriginalName()
        );

        $this->path = $this->getImgPage()->getClientOriginalName();

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
        /*
        $InfosPath = pathinfo($path);
        $extension = $InfosPath['extension'];
        $filename = $InfosPath['filename'];
        $Path = $this->removeAccents($filename).'.'.$extension;
        */

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

    public function removeAccents($string)
    {
        $text = str_replace(' ', '', $string);
        return strtr($text, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return SitePagesStandardSetting
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return SitePagesStandardSetting
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
