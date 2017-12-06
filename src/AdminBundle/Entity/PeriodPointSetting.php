<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\PeriodPointSettingRepository")
 * @ORM\Table(name="period_point_setting")
 */
class PeriodPointSetting
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="product_group", type="integer")
     */
    private $product_group;

    /**
     * @ORM\Column(name="gain", type="array", nullable=true)
     */
    private $gain;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="period_point_setting")
     */
    private $program;

    /**
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;

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
     * Set productGroup
     *
     * @param integer $productGroup
     *
     * @return PeriodPointSetting
     */
    public function setProductGroup($productGroup)
    {
        $this->product_group = $productGroup;

        return $this;
    }

    /**
     * Get productGroup
     *
     * @return integer
     */
    public function getProductGroup()
    {
        return $this->product_group;
    }

    /**
     * Set month
     *
     * @param integer $month
     *
     * @return PeriodPointSetting
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set gain
     *
     * @param float $gain
     *
     * @return PeriodPointSetting
     */
    public function setGain($gain)
    {
        $this->gain = $gain;

        return $this;
    }

    /**
     * Get gain
     *
     * @return float
     */
    public function getGain()
    {
        return $this->gain;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return PeriodPointSetting
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return PeriodPointSetting
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
}
