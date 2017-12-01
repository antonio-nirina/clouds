<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="roles")
     */
    private $program;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $network;

    /**
     * @ORM\Column(name="gain", type="float", nullable=true)
     */
    private $gain;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ProgramUser", mappedBy="role")
     */
    private $program_users;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->program_users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Role
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

    /**
     * Set rank
     *
     * @param integer $rank
     *
     * @return Role
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return Role
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
     * Add programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return Role
     */
    public function addProgramUser(\AdminBundle\Entity\ProgramUser $programUser)
    {
        $this->program_users[] = $programUser;

        return $this;
    }

    /**
     * Remove programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     */
    public function removeProgramUser(\AdminBundle\Entity\ProgramUser $programUser)
    {
        $this->program_users->removeElement($programUser);
    }

    /**
     * Get programUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgramUsers()
    {
        return $this->program_users;
    }

    /**
     * Set network
     *
     * @param string $network
     *
     * @return Role
     */
    public function setNetwork($network)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network
     *
     * @return string
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set gain
     *
     * @param float $gain
     *
     * @return Role
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
}
