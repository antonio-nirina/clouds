<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="sales")
 */
class Sales
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="product_name", type="string", length=255)
     */
    private $product_name;

    /**
     * @ORM\Column(name="product_group", type="integer", nullable=true)
     */
    private $product_group;

    /**
     * @ORM\Column(name="ca", type="float")
     */
    private $ca;

    /**
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $customization;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(name="date_from", type="datetime", nullable=true)
     */
    private $date_from;

    /**
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     */
    private $date_to;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ProgramUser")
     * @ORM\JoinColumn(name="program_user_id", referencedColumnName="id")
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
     * Set productName
     *
     * @param string $productName
     *
     * @return Sales
     */
    public function setProductName($productName)
    {
        $this->product_name = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * Set ca
     *
     * @param float $ca
     *
     * @return Sales
     */
    public function setCa($ca)
    {
        $this->ca = $ca;

        return $this;
    }

    /**
     * Get ca
     *
     * @return float
     */
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Sales
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set customization
     *
     * @param array $customization
     *
     * @return Sales
     */
    public function setCustomization($customization)
    {
        $this->customization = $customization;

        return $this;
    }

    /**
     * Get customization
     *
     * @return array
     */
    public function getCustomization()
    {
        return $this->customization;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Sales
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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return Sales
     */
    public function setDateFrom($dateFrom)
    {
        $this->date_from = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->date_from;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return Sales
     */
    public function setDateTo($dateTo)
    {
        $this->date_to = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->date_to;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return Sales
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
     * Set programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return Sales
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
     * Set productGroup
     *
     * @param integer $productGroup
     *
     * @return Sales
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
}
