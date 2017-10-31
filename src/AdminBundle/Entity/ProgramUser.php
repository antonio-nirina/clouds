<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="program_user")
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
}
