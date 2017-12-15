<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="")
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
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

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
     * Set month
     *
     * @param \DateTime $month
     *
     * @return ProgramUserClassmentProgression
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return \DateTime
     */
    public function getMonth()
    {
        return $this->month;
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
     * Set year
     *
     * @param integer $year
     *
     * @return ProgramUserClassmentProgression
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }
}
