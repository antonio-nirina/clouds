<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\PointAttributionSettingRepository")
 * @ORM\Table(name="point_attribution_setting")
 */
class PointAttributionSetting
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\PointAttributionType")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="point_attribution_setting")
     */
    private $program;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(name="min_value", type="float", nullable=true)
     * @Assert\Type(type="numeric", groups={"product_point"})
     */
    private $min_value;

    /**
     * @ORM\Column(name="max_value", type="float", nullable=true)
     * @Assert\Type(type="numeric", groups={"product_point"})
     */
    private $max_value;

    /**
     * @ORM\Column(name="gain", type="float", nullable=true)
     * @Assert\Type(type="numeric", groups={"product_point"})
     */
    private $gain;

    /**
     * @ORM\Column(name="product_group", type="integer", nullable=true)
     */
    private $product_group;

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
     * Set minValue
     *
     * @param string $minValue
     *
     * @return PointAttributionSetting
     */
    public function setMinValue($minValue)
    {
        $this->min_value = $minValue;

        return $this;
    }

    /**
     * Get minValue
     *
     * @return string
     */
    public function getMinValue()
    {
        return $this->min_value;
    }

    /**
     * Set maxValue
     *
     * @param string $maxValue
     *
     * @return PointAttributionSetting
     */
    public function setMaxValue($maxValue)
    {
        $this->max_value = $maxValue;

        return $this;
    }

    /**
     * Get maxValue
     *
     * @return string
     */
    public function getMaxValue()
    {
        return $this->max_value;
    }

    /**
     * Set gain
     *
     * @param string $gain
     *
     * @return PointAttributionSetting
     */
    public function setGain($gain)
    {
        $this->gain = $gain;

        return $this;
    }

    /**
     * Get gain
     *
     * @return string
     */
    public function getGain()
    {
        return $this->gain;
    }

    /**
     * Set productGroup
     *
     * @param integer $productGroup
     *
     * @return PointAttributionSetting
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
     * Set status
     *
     * @param string $status
     *
     * @return PointAttributionSetting
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
     * Set type
     *
     * @param \AdminBundle\Entity\PointAttributionType $type
     *
     * @return PointAttributionSetting
     */
    public function setType(\AdminBundle\Entity\PointAttributionType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AdminBundle\Entity\PointAttributionType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return PointAttributionSetting
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
     * Set name
     *
     * @param string $name
     *
     * @return PointAttributionSetting
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
