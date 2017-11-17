<?php

namespace AdminBundle\Repository;

class ProgramUserRepository extends \Doctrine\ORM\EntityRepository
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
}
