<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProgramUserRepository extends EntityRepository
{
    public function findOneByEmailAndProgram($email, $program)
    {
        $qb = $this->createQueryBuilder('program_user');
        $qb->addSelect('app_user')
            ->addSelect('program')
            ->join('program_user.app_user', 'app_user')
            ->join('program_user.program', 'program')
            ->where($qb->expr()->eq('app_user.email', ':email'))
            ->andWhere($qb->expr()->eq('program_user.program', ':program'))
            ->setParameter('email', $email)
            ->setParameter('program', $program);

        return $qb->getQuery()->getOneOrNullResult();
    }

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
