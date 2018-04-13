<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ProgramUserRepository")
 * @ORM\Table(name="program_user")
 */
class ProgramUser
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="programUser", cascade={"persist"})
     */
    private $app_user;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="program_users")
     */
    private $program;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\ProgramUser")
     */
    private $parent_program_user;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Role", inversedBy="program_users")
     */
    private $role;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AdminBundle\Entity\ProgramUserCompany",
     *     inversedBy="program_users",
     *     cascade={"persist"}
     *     )
     */
    private $program_user_company;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\UserPoint", mappedBy="program_user")
     */
    private $user_point;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ProgramUserClassmentProgression", mappedBy="program_user")
     */
    private $classment_progression;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $specialUseCaseState;

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
     * Set appUser
     *
     * @param \UserBundle\Entity\User $appUser
     *
     * @return ProgramUser
     */
    public function setAppUser(\UserBundle\Entity\User $appUser = null)
    {
        $this->app_user = $appUser;

        return $this;
    }

    /**
     * Get appUser
     *
     * @return \UserBundle\Entity\User
     */
    public function getAppUser()
    {
        return $this->app_user;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return ProgramUser
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
     * Set parentProgramUser
     *
     * @param \AdminBundle\Entity\ProgramUser $parentProgramUser
     *
     * @return ProgramUser
     */
    public function setParentProgramUser(\AdminBundle\Entity\ProgramUser $parentProgramUser = null)
    {
        $this->parent_program_user = $parentProgramUser;

        return $this;
    }

    /**
     * Get parentProgramUser
     *
     * @return \AdminBundle\Entity\ProgramUser
     */
    public function getParentProgramUser()
    {
        return $this->parent_program_user;
    }

    /**
     * Set role
     *
     * @param \AdminBundle\Entity\Role $role
     *
     * @return ProgramUser
     */
    public function setRole(\AdminBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AdminBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set programUserCompany
     *
     * @param \AdminBundle\Entity\ProgramUserCompany $programUserCompany
     *
     * @return ProgramUser
     */
    public function setProgramUserCompany(\AdminBundle\Entity\ProgramUserCompany $programUserCompany = null)
    {
        $this->program_user_company = $programUserCompany;

        return $this;
    }

    /**
     * Get programUserCompany
     *
     * @return \AdminBundle\Entity\ProgramUserCompany
     */
    public function getProgramUserCompany()
    {
        return $this->program_user_company;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user_point = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userPoint
     *
     * @param \AdminBundle\Entity\UserPoint $userPoint
     *
     * @return ProgramUser
     */
    public function addUserPoint(\AdminBundle\Entity\UserPoint $userPoint)
    {
        $this->user_point[] = $userPoint;

        return $this;
    }

    /**
     * Remove userPoint
     *
     * @param \AdminBundle\Entity\UserPoint $userPoint
     */
    public function removeUserPoint(\AdminBundle\Entity\UserPoint $userPoint)
    {
        $this->user_point->removeElement($userPoint);
    }

    /**
     * Get userPoint
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserPoint()
    {
        return $this->user_point;
    }

    /**
     * Add classmentProgression
     *
     * @param \AdminBundle\Entity\ProgramUserClassmentProgression $classmentProgression
     *
     * @return ProgramUser
     */
    public function addClassmentProgression(\AdminBundle\Entity\ProgramUserClassmentProgression $classmentProgression)
    {
        $this->classment_progression[] = $classmentProgression;

        return $this;
    }

    /**
     * Remove classmentProgression
     *
     * @param \AdminBundle\Entity\ProgramUserClassmentProgression $classmentProgression
     */
    public function removeClassmentProgression(\AdminBundle\Entity\ProgramUserClassmentProgression $classmentProgression)
    {
        $this->classment_progression->removeElement($classmentProgression);
    }

    /**
     * Get classmentProgression
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClassmentProgression()
    {
        return $this->classment_progression;
    }

    /**
     * Set specialUseCaseState
     *
     * @param boolean $specialUseCaseState
     *
     * @return ProgramUser
     */
    public function setSpecialUseCaseState($specialUseCaseState)
    {
        $this->specialUseCaseState = $specialUseCaseState;

        return $this;
    }

    /**
     * Get specialUseCaseState
     *
     * @return boolean
     */
    public function getSpecialUseCaseState()
    {
        return $this->specialUseCaseState;
    }
}
