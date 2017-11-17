<?php

namespace AdminBundle\Repository;

class ProgramUserCompanyRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneByNameAndProgram($name, $program)
    {
        $qb = $this->createQueryBuilder('program_user_company');
        $qb->addSelect('program_users')
            ->addSelect('program')
            ->join('program_user_company.program_users', 'program_users')
            ->join('program_users.program', 'program')
            ->where($qb->expr()->eq('program_user_company.name', ':name'))
            ->andWhere($qb->expr()->eq('program', ':program'))
            ->setParameter('name', $name)
            ->setParameter('program', $program);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
