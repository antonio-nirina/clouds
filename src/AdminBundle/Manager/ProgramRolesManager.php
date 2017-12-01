<?php

namespace AdminBundle\Manager;

use AdminBundle\Entity\Program;
use AdminBundle\Entity\Role;
use Doctrine\ORM\EntityManager;

class ProgramRolesManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setAllRoleRank(Program $program)
    {
        $roles = $program->getRoles();

        if (count($roles) >0) { //vider
            foreach ($roles as $role) {
                $roles->removeElement($role);
            }
        }

        for ($i=2; $i<=6; $i++) {
            $role = $this->em->getRepository("AdminBundle:Role")->findBy(
                array(
                    'program' => $program,
                    'rank' => $i
                )
            );

            if ($role) {
                $roles->add($role);
            } else {
                $role = new Role();
                $role->setRank($i);
                $role->setProgram($program);
                $roles->add($role);
            }
        }

        return $program;
    }
}
