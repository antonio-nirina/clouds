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

        // if (count($roles) >0) { //vider
        //     foreach ($roles as $role) {
        //         $program->getRoles()->removeElement($role);
        //     }
        // }

        for ($i=2; $i<=6; $i++) {
            $role = $this->em->getRepository("AdminBundle:Role")->findBy(
                array(
                    'program' => $program,
                    'rank' => $i
                )
            );

            if ($role) {
                $program->getRoles()->add($role[0]);
            } else {
                $role = new Role();
                $role->setRank($i);
                $role->setProgram($program);
                $program->getRoles()->add($role);
            }
        }

        return $program;
    }

    public function saveAllRoleRank(Program $program)
    {
        $roles = $program->getRoles();

        foreach ($roles as $role) {
            if (empty($role->getName())) {
                if ($role->getId()) {
                    $this->em->remove($role);
                } else {
                    $program->getRoles()->removeElement($role);
                }
            }
        }

        $this->em->flush();
        return $program;
    }
}
