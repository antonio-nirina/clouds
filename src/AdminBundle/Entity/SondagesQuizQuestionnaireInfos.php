<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SondagesQuizQuestionnaireInfosRepository")
 * @ORM\Table(name="sondages_quiz_questionnaire_infos")
 * @ORM\HasLifecycleCallbacks()
 */
class SondagesQuizQuestionnaireInfos
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $type_sondages_quiz;
	
	/**
     * @ORM\Column(type="string", nullable=true)
     */
    private $titre_questionnaire;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description_questionnaire;
	
	/**
     * @ORM\Column(name="date_creation", type="datetime")
     */
    protected $date_creation;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuiz", inversedBy="sondages_quiz_questionnaire_infos")
     */
    private $sondages_quiz;
	
	/**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuizQuestions", cascade={"remove"}, mappedBy="sondages_quiz_questionnaire_infos")
     */
    private $sondages_quiz_questions;
	
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->sondages_quiz_questions = new ArrayCollection();
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
     * Set typeSondagesQuiz
     *
     * @param boolean $typeSondagesQuiz
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setTypeSondagesQuiz($typeSondagesQuiz)
    {
        $this->type_sondages_quiz = $typeSondagesQuiz;

        return $this;
    }

    /**
     * Get typeSondagesQuiz
     *
     * @return boolean
     */
    public function getTypeSondagesQuiz()
    {
        return $this->type_sondages_quiz;
    }

    /**
     * Set titreQuestionnaire
     *
     * @param string $titreQuestionnaire
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setTitreQuestionnaire($titreQuestionnaire)
    {
        $this->titre_questionnaire = $titreQuestionnaire;

        return $this;
    }

    /**
     * Get titreQuestionnaire
     *
     * @return string
     */
    public function getTitreQuestionnaire()
    {
        return $this->titre_questionnaire;
    }

    /**
     * Set descriptionQuestionnaire
     *
     * @param string $descriptionQuestionnaire
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setDescriptionQuestionnaire($descriptionQuestionnaire)
    {
        $this->description_questionnaire = $descriptionQuestionnaire;

        return $this;
    }

    /**
     * Get descriptionQuestionnaire
     *
     * @return string
     */
    public function getDescriptionQuestionnaire()
    {
        return $this->description_questionnaire;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return SondagesQuizQuestionnaireInfos
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
	* @ORM\PrePersist
	*/
	public function addDateCreation()
	{
		$this->setDateCreation(new \Datetime());
	}

    /**
     * Set sondagesQuiz
     *
     * @param \AdminBundle\Entity\SondagesQuiz $sondagesQuiz
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setSondagesQuiz(\AdminBundle\Entity\SondagesQuiz $sondagesQuiz = null)
    {
        $this->sondages_quiz = $sondagesQuiz;

        return $this;
    }

    /**
     * Get sondagesQuiz
     *
     * @return \AdminBundle\Entity\SondagesQuiz
     */
    public function getSondagesQuiz()
    {
        return $this->sondages_quiz;
    }

    /**
     * Add sondagesQuizQuestion
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestion
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function addSondagesQuizQuestion(\AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestion)
    {
        $this->sondages_quiz_questions[] = $sondagesQuizQuestion;

        return $this;
    }

    /**
     * Remove sondagesQuizQuestion
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestion
     */
    public function removeSondagesQuizQuestion(\AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestion)
    {
        $this->sondages_quiz_questions->removeElement($sondagesQuizQuestion);
    }

    /**
     * Get sondagesQuizQuestions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSondagesQuizQuestions()
    {
        return $this->sondages_quiz_questions;
    }
}
