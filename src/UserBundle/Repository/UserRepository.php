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

    public function findUserByMail($email)
    {
        $qb = $this->createQueryBuilder('user');
        $qb->where($qb->expr()->like('user.email', ':email'))->setParameter('email', '' . $email . '');
        return $qb->getQuery()->getResult();
    }

    /*
    public function findAllUserWithRoleList($roles){
    $q = $this->createQueryBuilder('user');
    $q->where($q->expr()->In('user.roles', ':roles'))->setParameter('roles', $roles);
    //$q->where('user.roles IN (:roles)')->setParameter('roles', $roles);

    return $q->getQuery()->getResult();
    }
    */
}
