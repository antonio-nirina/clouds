<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SondagesQuizReponsesRepository")
 * @ORM\Table(name="sondages_quiz_reponses")
 * @ORM\HasLifecycleCallbacks()
 */
class SondagesQuizReponses
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
    private $reponses;

    /**
     * @ORM\Column(type="boolean")
     */
    private $est_bonne_reponse;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SondagesQuizQuestions", inversedBy="sondages_quiz_reponses")
     */
    private $sondages_quiz_questions;

    /**
     * @ORM\Column(name="date_creation", type="datetime")
     */
    protected $date_creation;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ResultatsSondagesQuiz", cascade={"remove"}, mappedBy="sondages_quiz_reponses")
     */
    private $resultatsSondagesQuiz;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resultatsSondagesQuiz = new ArrayCollection();
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
     * Set reponses
     *
     * @param string $reponses
     *
     * @return SondagesQuizReponses
     */
    public function setReponses($reponses)
    {
        $this->reponses = $reponses;

        return $this;
    }

    /**
     * Get reponses
     *
     * @return string
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Set estBonneReponse
     *
     * @param boolean $estBonneReponse
     *
     * @return SondagesQuizReponses
     */
    public function setEstBonneReponse($estBonneReponse)
    {
        $this->est_bonne_reponse = $estBonneReponse;

        return $this;
    }

    /**
     * Get estBonneReponse
     *
     * @return boolean
     */
    public function getEstBonneReponse()
    {
        return $this->est_bonne_reponse;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return SondagesQuizReponses
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return SondagesQuizReponses
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
     * Set sondagesQuizQuestions
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestions $sondagesQuizQuestions
     *
     * @return SondagesQuizReponses
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
     * Add resultatsSondagesQuiz
     *
     * @param \AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz
     *
     * @return ResultatsSondagesQuiz
     */
    public function addResultatsSondagesQuiz(\AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz)
    {
        $this->resultatsSondagesQuiz[] = $resultatsSondagesQuiz;

        return $this;
    }

    /**
     * Remove resultatsSondagesQuiz
     *
     * @param \AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz
     */
    public function removeResultatsSondagesQuiz(\AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz)
    {
        $this->resultatsSondagesQuiz->removeElement($resultatsSondagesQuiz);
    }

    /**
     * Get resultatsSondagesQuiz
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultatsSondagesQuiz()
    {
        return $this->resultatsSondagesQuiz;
    }
}
