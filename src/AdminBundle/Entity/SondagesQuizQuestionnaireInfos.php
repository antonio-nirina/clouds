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
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuizQuestions", mappedBy="sondages_quiz_questionnaire_infos")
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
     * Set type_sondages_quiz
     *
     * @param string $type_sondages_quiz
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setTypeSondagesQuiz($type_sondages_quiz)
    {
        $this->type_sondages_quiz = $type_sondages_quiz;

        return $this;
    }
	
	/**
     * Get type_sondages_quiz
     *
     * @return string
     */
    public function getTypeSondagesQuiz()
    {
        return $this->type_sondages_quiz;
    }
	
	/**
     * Set titre_questionnaire
     *
     * @param string $titre_questionnaire
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setTitreQuestionnaire($titre_questionnaire)
    {
        $this->titre_questionnaire = $titre_questionnaire;

        return $this;
    }
	
	/**
     * Get titre_questionnaire
     *
     * @return string
     */
    public function getTitreQuestionnaire()
    {
        return $this->titre_questionnaire;
    }
	
	
	/**
     * Set description_questionnaire
     *
     * @param string $description_questionnaire
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setDescriptionQuestionnaire($description_questionnaire)
    {
        $this->description_questionnaire = $description_questionnaire;

        return $this;
    }
	
	/**
     * Get description_questionnaire
     *
     * @return string
     */
    public function getDescriptionQuestionnaire()
    {
        return $this->description_questionnaire;
    }
	
	
	/**
     * Set date_creation
     *
     * @param datetime $date_creation
     *
     * @return SondagesQuizQuestionnaireInfos
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
     * Set sondagesQuiz
     *
     * @param \AdminBundle\Entity\SondagesQuiz $sondagesQuiz
     *
     * @return SondagesQuiz
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
     * Add sondagesQuizQuestions
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions
     *
     * @return SondagesQuizQuestions
     */
    public function addSondagesQuizQuestions(\AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions)
    {
        $this->sondages_quiz_questions[] = $sondagesQuizQuestions;

        return $this;
    }

    /**
     * Remove sondagesQuizQuestions
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions
     */
    public function removeSondagesQuizQuestions(\AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions)
    {
        $this->sondages_quiz_questions->removeElement($sondagesQuizQuestions);
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
