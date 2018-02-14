<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SondagesQuizReponsesRepository")
 * @ORM\Table(name="sondages_quiz_reponses")
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
     * Set est_bonne_reponse
     *
     * @param string $est_bonne_reponse
     *
     * @return SondagesQuizReponses
     */
    public function setEstBonneReponse($est_bonne_reponse)
    {
        $this->est_bonne_reponse = $est_bonne_reponse;

        return $this;
    }
	
	/**
     * Get est_bonne_reponse
     *
     * @return string
     */
    public function getEstBonneReponse()
    {
        return $this->est_bonne_reponse;
    }
	
	/**
     * Set ordre
     *
     * @param string $ordre
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
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }
	
	/**
     * Set sondages_quiz_questions
     *
     * @param string $sondages_quiz_questions
     *
     * @return SondagesQuizReponses
     */
    public function setSondagesQuizQuestions($sondages_quiz_questions)
    {
        $this->sondages_quiz_questions = $sondages_quiz_questions;

        return $this;
    }
	
	/**
     * Get sondages_quiz_questions
     *
     * @return string
     */
    public function getSondagesQuizQuestions()
    {
        return $this->sondages_quiz_questions;
    }
	
	
	/**
     * Set date_creation
     *
     * @param datetime $date_creation
     *
     * @return SondagesQuizReponses
     */
    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * Get date_creation
     *
     * @return datetime
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }
}
