<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ProgramUserClassmentProgressionRepository")
 * @ORM\Table(name="program_user_classment_progression")
 */
class ProgramUserClassmentProgression
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $start_date;

    /**
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $end_date;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $last_update;

    /**
     * @ORM\Column(name="classment", type="integer", nullable=true)
     */
    private $classment;

    /**
     * @ORM\Column(name="progression", type="float", length=255, nullable=true)
     */
    private $progression;

    /**
     * @ORM\Column(name="previous_ca", type="string", length=255, nullable=true)
     */
    private $previous_ca;

    /**
     * @ORM\Column(name="current_ca", type="string", length=255, nullable=true)
     */
    private $current_ca;

    /**
     * @ORM\Column(name="earn_points", type="float", nullable=true)
     */
    private $earn_points;

    /**
     * @ORM\Column(name="previous_points", type="float", nullable=true)
     */
    private $previous_points;

    /**
     * @ORM\Column(name="is_previous", type="boolean", nullable=true)
     */
    private $is_previous;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ProgramUser", inversedBy="classment_progression")
     */
    private $program_user;

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
     * Set classment
     *
     * @param integer $classment
     *
     * @return ProgramUserClassmentProgression
     */
    public function setClassment($classment)
    {
        $this->classment = $classment;

        return $this;
    }

    /**
     * Get classment
     *
     * @return integer
     */
    public function getClassment()
    {
        return $this->classment;
    }

    /**
     * Set progression
     *
     * @param string $progression
     *
     * @return ProgramUserClassmentProgression
     */
    public function setProgression($progression)
    {
        $this->progression = $progression;

        return $this;
    }

    /**
     * Get progression
     *
     * @return string
     */
    public function getProgression()
    {
        return $this->progression;
    }

    /**
     * Set previousCa
     *
     * @param string $previousCa
     *
     * @return ProgramUserClassmentProgression
     */
    public function setPreviousCa($previousCa)
    {
        $this->previous_ca = $previousCa;

        return $this;
    }

    /**
     * Get previousCa
     *
     * @return string
     */
    public function getPreviousCa()
    {
        return $this->previous_ca;
    }

    /**
     * Set currentCa
     *
     * @param string $currentCa
     *
     * @return ProgramUserClassmentProgression
     */
    public function setCurrentCa($currentCa)
    {
        $this->current_ca = $currentCa;

        return $this;
    }

    /**
     * Get currentCa
     *
     * @return string
     */
    public function getCurrentCa()
    {
        return $this->current_ca;
    }

    /**
     * Set programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return ProgramUserClassmentProgression
     */
    public function setProgramUser(\AdminBundle\Entity\ProgramUser $programUser = null)
    {
        $this->program_user = $programUser;

        return $this;
    }

    /**
     * Get programUser
     *
     * @return \AdminBundle\Entity\ProgramUser
     */
    public function getProgramUser()
    {
        return $this->program_user;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return ProgramUserClassmentProgression
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->last_update = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * Set earnPoints
     *
     * @param float $earnPoints
     *
     * @return ProgramUserClassmentProgression
     */
    public function setEarnPoints($earnPoints)
    {
        $this->earn_points = $earnPoints;

        return $this;
    }

    /**
     * Get earnPoints
     *
     * @return float
     */
    public function getEarnPoints()
    {
        return $this->earn_points;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return ProgramUserClassmentProgression
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return ProgramUserClassmentProgression
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set previousPoints
     *
     * @param float $previousPoints
     *
     * @return ProgramUserClassmentProgression
     */
    public function setPreviousPoints($previousPoints)
    {
        $this->previous_points = $previousPoints;

        return $this;
    }

    /**
     * Get previousPoints
     *
     * @return float
     */
    public function getPreviousPoints()
    {
        return $this->previous_points;
    }

    /**
     * Set isPrevious
     *
     * @param boolean $isPrevious
     *
     * @return ProgramUserClassmentProgression
     */
    public function setIsPrevious($isPrevious)
    {
        $this->is_previous = $isPrevious;

        return $this;
    }

    /**
     * Get isPrevious
     *
     * @return boolean
     */
    public function getIsPrevious()
    {
        return $this->is_previous;
    }
}
