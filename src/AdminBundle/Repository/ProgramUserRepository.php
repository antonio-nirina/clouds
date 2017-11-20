<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProgramUserRepository extends EntityRepository
{
    public function findByNameAndLastName($name, $firstname, $program)
    {
        $qb = $this->createQueryBuilder('pu');
        $qb->select('pu')
            ->join('pu.program', 'prg')
            ->join('pu.app_user', 'app_user')
            ->where($qb->expr()->eq('app_user.name', ':name'))
            ->andWhere($qb->expr()->eq('app_user.firstname', ':firstname'))
            ->andWhere($qb->expr()->eq('pu.program', ':program'))
            ->setParameters(array(
                                "name" => $name,
                                "firstname" => $firstname,
                                "program" => $program
                                 ));

        return $qb->getQuery()->getResult();
    }
}
