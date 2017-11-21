<?php

namespace UserBundle\Repository;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findSuperAdmin()
    {
        $qb = $this->createQueryBuilder('user');
        $qb->where($qb->expr()->like('user.roles', ':role'))
            ->setParameter('role', '%ROLE_SUPER_ADMIN%');

        return $qb->getQuery()->getResult();
    }
}
