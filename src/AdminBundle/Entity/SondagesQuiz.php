<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SondagesQuizRepository")
 * @ORM\Table(name="sondages_quiz")
 */
class SondagesQuiz
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
    private $nom_menu;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    private $titre;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
	
	/**
     * @Assert\File(maxSize="6000000")
     */
    private $image;
	
	/**
     * @Gedmo\Slug(fields={"nom_menu"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;
	
	/**
     * @ORM\Column(name="date_creation", type="datetime")
     */
    protected $date_creation;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="sondages_quiz")
     */
    private $program;
	
	/**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuizQuestionnaireInfos", mappedBy="sondages_quiz")
     */
    private $sondages_quiz_questionnaire_infos;
	
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->sondages_quiz_questionnaire_infos = new ArrayCollection();
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
     * Set nom_menu
     *
     * @param string $nom_menu
     *
     * @return SondagesQuiz
     */
    public function setNomMenu($nom_menu)
    {
        $this->nom_menu = $nom_menu;

        return $this;
    }
	
	/**
     * Get nom_menu
     *
     * @return string
     */
    public function getNomMenu()
    {
        return $this->nom_menu;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return SondagesQuiz
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return SondagesQuiz
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
     * Sets image.
     *
     * @param UploadedFile $image
     */
    public function setImage(UploadedFile $image = null)
    {
        $this->image = $image;
    }

    /**
     * Get image.
     *
     * @return UploadedFile
     */
    public function getImage()
    {
        return $this->image;
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

    public function getUploadRootDir()
    {
        return __DIR__.'/../../../web/content/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'sondages_quiz';
    }
	
	public function upload(\AdminBundle\Entity\Program $program = null)
	{
		if (null === $this->getImage()) {
			return;
		}


		$this->getImage()->move(
			$this->getUploadRootDir().'/'.$program->getId(),
			$this->getImage()->getClientOriginalName()
		);

		
		$this->path = $this->getImage()->getClientOriginalName();

		
		$this->image = null;
	}
	
	/**
     * Set Path
     *
     * @param string $path
     *
     * @return SondagesQuiz
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
	
	public function removeAccents($string)
	{
		$text = str_replace(' ','',$string);
		return strtr($text,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
	}
	
	/**
     * Set slug
     *
     * @param string $slug
     *
     * @return SondagesQuiz
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return SondagesQuiz
     */
    public function getSlug()
    {
        return $this->slug;
    }
	
	
	/**
     * Set date_creation
     *
     * @param datetime $date_creation
     *
     * @return SondagesQuiz
     */
    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * Get date_creation
     *
     * @return SondagesQuiz
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }
	
	
	/**
     * Add sondagesQuizQuestionnaireInfos
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function addSondagesQuizQuestionnaireInfos(\AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos)
    {
        $this->sondages_quiz_questionnaire_infos[] = $sondagesQuizQuestionnaireInfos;

        return $this;
    }

    /**
     * Remove sondagesQuizQuestionnaireInfos
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos
     */
    public function removeSondagesQuizQuestionnaireInfos(\AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos)
    {
        $this->sondages_quiz_questionnaire_infos->removeElement($SondagesQuizQuestionnaireInfos);
    }

    /**
     * Get sondagesQuizQuestionnaireInfos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSondagesQuizQuestionnaireInfos()
    {
        return $this->sondages_quiz_questionnaire_infos;
    }
}
