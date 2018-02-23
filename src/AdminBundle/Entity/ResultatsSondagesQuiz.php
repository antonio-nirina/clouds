<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ResultatsSondagesQuizRepository")
 * @ORM\Table(name="resultats_sondages_quiz")
 * @ORM\HasLifecycleCallbacks()
 */
class ResultatsSondagesQuiz
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="resultats_sondages_quiz")
     */
    private $program;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuiz", inversedBy="resultats_sondages_quiz")
     */
    private $sondages_quiz;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuizQuestionnaireInfos", inversedBy="resultats_sondages_quiz")
     */
    private $sondages_quiz_questionnaire_infos;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuizQuestions", inversedBy="resultats_sondages_quiz")
     */
    private $sondages_quiz_questions;
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuizReponses", inversedBy="resultats_sondages_quiz")
     */
    private $sondages_quiz_reponses;
	
	/**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="resultats_sondages_quiz")
     */
    private $user;
	
	/**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $echelle;
	
	/**
     * @ORM\Column(name="date_creation", type="datetime")
     */
    protected $date_creation;

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
     * Set echelle
     *
     * @param integer $echelle
     *
     * @return ResultatsSondagesQuiz
     */
    public function setEchelle($echelle)
    {
        $this->echelle = $echelle;

        return $this;
    }

    /**
     * Get echelle
     *
     * @return integer
     */
    public function getEchelle()
    {
        return $this->echelle;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return ResultatsSondagesQuiz
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
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return ResultatsSondagesQuiz
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
     * Set sondagesQuiz
     *
     * @param \AdminBundle\Entity\SondagesQuiz $sondagesQuiz
     *
     * @return ResultatsSondagesQuiz
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
     * Set sondagesQuizQuestionnaireInfos
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestionnaireInfos $sondagesQuizQuestionnaireInfos
     *
     * @return ResultatsSondagesQuiz
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
     * Set sondagesQuizQuestions
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions
     *
     * @return ResultatsSondagesQuiz
     */
    public function setSondagesQuizQuestions(\AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions = null)
    {
        $this->sondages_quiz_questions = $sondagesQuizQuestions;

        return $this;
    }

    /**
     * Get sondagesQuizQuestions
     *
     * @return \AdminBundle\Entity\SondagesQuizQuestions
     */
    public function getSondagesQuizQuestions()
    {
        return $this->sondages_quiz_questions;
    }

    /**
     * Set sondagesQuizReponses
     *
     * @param \AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponses
     *
     * @return ResultatsSondagesQuiz
     */
    public function setSondagesQuizReponses(\AdminBundle\Entity\SondagesQuizReponses $sondagesQuizReponses = null)
    {
        $this->sondages_quiz_reponses = $sondagesQuizReponses;

        return $this;
    }

    /**
     * Get sondagesQuizReponses
     *
     * @return \AdminBundle\Entity\SondagesQuizReponses
     */
    public function getSondagesQuizReponses()
    {
        return $this->sondages_quiz_reponses;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return ResultatsSondagesQuiz
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
	
	/**
	* @ORM\PrePersist
	*/
	public function addDateCreation()
	{
		$this->setDateCreation(new \Datetime());
	}
}
