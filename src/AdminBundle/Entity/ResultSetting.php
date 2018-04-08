<?php

namespace AdminBundle\Entity;

use AdminBundle\Entity\Program;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ResultSettingRepository")
 * @ORM\Table(name="result_settings")
 */
class ResultSetting
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="monthly", type="boolean", nullable=true)
     */
    private $monthly;

    /**
     * @ORM\Column(name="by_product", type="boolean", nullable=true)
     */
    private $by_product;

    /**
     * @ORM\Column(name="by_rank", type="boolean", nullable=true)
     */
    private $by_rank;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program", cascade={"persist"})
     * @ORM\JoinColumn(nullable = false)
     */
    private $program;

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
     * Set monthly
     *
     * @param boolean $monthly
     *
     * @return ResultSetting
     */
    public function setMonthly($monthly)
    {
        $this->monthly = $monthly;

        return $this;
    }

    /**
     * Get monthly
     *
     * @return boolean
     */
    public function getMonthly()
    {
        return $this->monthly;
    }

    /**
     * Set byProduct
     *
     * @param boolean $byProduct
     *
     * @return ResultSetting
     */
    public function setByProduct($byProduct)
    {
        $this->by_product = $byProduct;

        return $this;
    }

    /**
     * Get byProduct
     *
     * @return boolean
     */
    public function getByProduct()
    {
        return $this->by_product;
    }

    /**
     * Set byRank
     *
     * @param boolean $byRank
     *
     * @return ResultSetting
     */
    public function setByRank($byRank)
    {
        $this->by_rank = $byRank;

        return $this;
    }

    /**
     * Get byRank
     *
     * @return boolean
     */
    public function getByRank()
    {
        return $this->by_rank;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return ResultSetting
     */
    public function setProgram(Program $program)
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
