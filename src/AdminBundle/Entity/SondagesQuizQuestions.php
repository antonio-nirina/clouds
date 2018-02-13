<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SondagesQuizQuestionsRepository")
 * @ORM\Table(name="sondages_quiz_questions")
 */
class SondagesQuizQuestions
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
    private $questions;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentaire;
	
	/**
     * @ORM\Column(type="boolean")
     */
    private $type_question;
	
	/**
     * @ORM\Column(type="boolean")
     */
    private $est_reponse_obligatoire;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuizQuestionnaireInfos", inversedBy="sondages_quiz_questions")
     */
    private $sondages_quiz_questionnaire_infos;
	
	/**
     * @ORM\Column(name="date_creation", type="datetime")
     */
    protected $date_creation;
	
	/**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuizReponses", mappedBy="sondages_quiz_questions")
     */
    private $sondages_quiz_reponses;
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->sondages_quiz_reponses = new ArrayCollection();
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
     * Set questions
     *
     * @param string $questions
     *
     * @return SondagesQuizQuestions
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;

        return $this;
    }
	
	/**
     * Get questions
     *
     * @return string
     */
    public function getQuestions()
    {
        return $this->questions;
    }
	
	/**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return SondagesQuizQuestions
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }
	
	/**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }
	
	/**
     * Set type_question
     *
     * @param string $type_question
     *
     * @return SondagesQuizQuestions
     */
    public function setTypeQuestion($type_question)
    {
        $this->type_question = $type_question;

        return $this;
    }
	
	/**
     * Get type_question
     *
     * @return string
     */
    public function getTypeQuestion()
    {
        return $this->type_question;
    }
	
	/**
     * Set est_reponse_obligatoire
     *
     * @param string $est_reponse_obligatoire
     *
     * @return SondagesQuizQuestions
     */
    public function setEstReponseObligatoire($est_reponse_obligatoire)
    {
        $this->est_reponse_obligatoire = $est_reponse_obligatoire;

        return $this;
    }
	
	/**
     * Get est_reponse_obligatoire
     *
     * @return string
     */
    public function getEstReponseObligatoire()
    {
        return $this->est_reponse_obligatoire;
    }
	
	/**
     * Set sondages_quiz_questionnaire_infos
     *
     * @param string $sondages_quiz_questionnaire_infos
     *
     * @return SondagesQuizQuestions
     */
    public function setSondagesQuizQuestionnaireInfos($sondages_quiz_questionnaire_infos)
    {
        $this->sondages_quiz_questionnaire_infos = $sondages_quiz_questionnaire_infos;

        return $this;
    }
	
	/**
     * Get sondages_quiz_questionnaire_infos
     *
     * @return string
     */
    public function getSondagesQuizQuestionnaireInfos()
    {
        return $this->sondages_quiz_questionnaire_infos;
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
     * Add sondagesQuizReponses
     *
     * @param \AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponses
     *
     * @return SondagesQuizReponses
     */
    public function addSondagesQuizReponses(\AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponses)
    {
        $this->sondages_quiz_reponses[] = $sondagesQuizReponses;

        return $this;
    }

    /**
     * Remove sondagesQuizReponses
     *
     * @param \AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponses
     */
    public function removeSondagesQuizReponses(\AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponses)
    {
        $this->sondages_quiz_reponses->removeElement($sondagesQuizReponses);
    }

    /**
     * Get sondagesQuizReponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSondagesQuizReponses()
    {
        return $this->sondages_quiz_reponses;
    }
}
