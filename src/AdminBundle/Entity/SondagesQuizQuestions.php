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
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="integer")
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
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuizReponses", cascade={"persist", "remove"}, mappedBy="sondages_quiz_questions")
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
     * Set typeQuestion
     *
     * @param boolean $typeQuestion
     *
     * @return SondagesQuizQuestions
     */
    public function setTypeQuestion($typeQuestion)
    {
        $this->type_question = $typeQuestion;

        return $this;
    }

    /**
     * Get typeQuestion
     *
     * @return boolean
     */
    public function getTypeQuestion()
    {
        return $this->type_question;
    }

    /**
     * Set estReponseObligatoire
     *
     * @param boolean $estReponseObligatoire
     *
     * @return SondagesQuizQuestions
     */
    public function setEstReponseObligatoire($estReponseObligatoire)
    {
        $this->est_reponse_obligatoire = $estReponseObligatoire;

        return $this;
    }

    /**
     * Get estReponseObligatoire
     *
     * @return boolean
     */
    public function getEstReponseObligatoire()
    {
        return $this->est_reponse_obligatoire;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return SondagesQuizQuestions
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
     * Set sondagesQuizQuestionnaireInfos
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos
     *
     * @return SondagesQuizQuestions
     */
    public function setSondagesQuizQuestionnaireInfos(\AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos = null)
    {
        $this->sondages_quiz_questionnaire_infos = $sondagesQuizQuestionnaireInfos;

        return $this;
    }

    /**
     * Get sondagesQuizQuestionnaireInfos
     *
     * @return \AdminBundle\Entity\SondagesQuizQuestionnaireInfos
     */
    public function getSondagesQuizQuestionnaireInfos()
    {
        return $this->sondages_quiz_questionnaire_infos;
    }

    /**
     * Add sondagesQuizReponse
     *
     * @param \AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponse
     *
     * @return SondagesQuizQuestions
     */
    public function addSondagesQuizReponse(\AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponse)
    {
        $this->sondages_quiz_reponses[] = $sondagesQuizReponse;

        return $this;
    }

    /**
     * Remove sondagesQuizReponse
     *
     * @param \AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponse
     */
    public function removeSondagesQuizReponse(\AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponse)
    {
        $this->sondages_quiz_reponses->removeElement($sondagesQuizReponse);
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
