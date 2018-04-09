<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UserPointRepository")
 * @ORM\Table(name="user_point")
 */
class UserPoint
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ProgramUser", inversedBy="user_point")
     */
    private $program_user;

    /**
     * @ORM\Column(name="points", type="string", length=255, nullable=true)
     */
    private $points;

    /**
     * @ORM\Column(name="amount", type="string", length=255, nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(name="motif", type="text", nullable=true)
     */
    private $motif;

    /**
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;

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
     * Set points
     *
     * @param string $points
     *
     * @return UserPoint
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return string
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return UserPoint
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return UserPoint
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set motif
     *
     * @param string $motif
     *
     * @return UserPoint
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return UserPoint
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
     * Set reference
     *
     * @param string $reference
     *
     * @return UserPoint
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
