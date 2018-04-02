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
     * @ORM\Column(type="integer")
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
     * @ORM\Column(name="date_creation", type="datetime",nullable=true)
     */
    protected $date_creation;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuiz", inversedBy="sondages_quiz_questionnaire_infos",cascade={"persist"})
     */
    private $sondages_quiz;
	
	/**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SondagesQuizQuestions", cascade={"remove"}, mappedBy="sondages_quiz_questionnaire_infos")
	 * @ORM\OrderBy({"ordre" = "ASC"})
     */
    private $sondages_quiz_questions;
	
	/**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ResultatsSondagesQuiz", mappedBy="sondages_quiz_questionnaire_infos")
     */
    private $resultats_sondages_quiz;
	
	/**
     * @ORM\Column(type="boolean")
     */
    private $est_publier;

    /**
     * @ORM\Column(type="boolean",options={"default"=0})
     */
    private $est_archived = false;

    /**
     * @ORM\Column(type="boolean",options={"default"=0})
     */
    private $est_cloture = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $view_number;

    /**
     * @var string $viewer_authorization_type   available value in AdminBundle\Component\Post\NewsPostAuthorizationType
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=100)
     * @Assert\NotBlank()
     */
    private $authorization_type;

    /**
     * @var string $authorized_viewer_role          AdminBundle\Entity\ProgramUser role value
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $authorized_role;

	
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->sondages_quiz_questions = new ArrayCollection();
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
    * @ORM\PrePersist()
    */
	public function addDateCreation()
	{
		$this->date_creation = new \Datetime();
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
	
	/**
     * Set estPublier
     *
     * @param boolean $estPublier
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setEstPublier($estPublier)
    {
        $this->est_publier = $estPublier;

        return $this;
    }

    /**
     * Get estPublier
     *
     * @return boolean
     */
    public function getEstPublier()
    {
        return $this->est_publier;
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
     * Get sondagesQuizQuestions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultatsSondagesQuiz()
    {
        return $this->resultats_sondages_quiz;
    }

   

    /**
     * Set estArchived.
     *
     * @param bool $estArchived
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setEstArchived($estArchived)
    {
        $this->est_archived = $estArchived;

        return $this;
    }

    /**
     * Get estArchived.
     *
     * @return bool
     */
    public function getEstArchived()
    {
        return $this->est_archived;
    }

    /**
     * Set estCloture.
     *
     * @param bool $estCloture
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setEstCloture($estCloture)
    {
        $this->est_cloture = $estCloture;

        return $this;
    }

    /**
     * Get estCloture.
     *
     * @return bool
     */
    public function getEstCloture()
    {
        return $this->est_cloture;
    }

    /**
     * Set viewNumber.
     *
     * @param int|null $viewNumber
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setViewNumber($viewNumber = null)
    {
        $this->view_number = $viewNumber;

        return $this;
    }

    /**
     * Get viewNumber.
     *
     * @return int|null
     */
    public function getViewNumber()
    {
        return $this->view_number;
    }

    /**
     * Set authorizationType.
     *
     * @param string $authorizationType
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setAuthorizationType($authorizationType)
    {
        $this->authorization_type = $authorizationType;

        return $this;
    }

    /**
     * Get authorizationType.
     *
     * @return string
     */
    public function getAuthorizationType()
    {
        return $this->authorization_type;
    }

    /**
     * Set authorizedRole.
     *
     * @param string|null $authorizedRole
     *
     * @return SondagesQuizQuestionnaireInfos
     */
    public function setAuthorizedRole($authorizedRole = null)
    {
        $this->authorized_role = $authorizedRole;

        return $this;
    }

    /**
     * Get authorizedRole.
     *
     * @return string|null
     */
    public function getAuthorizedRole()
    {
        return $this->authorized_role;
    }
}
