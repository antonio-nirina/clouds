<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    public function findHigherRank($program, $rank)
    {
        $qb = $this->createQueryBuilder('role');
        $qb ->where($qb->expr()->eq('role.program', ':program'))
            ->andWhere($qb->expr()->lt('role.rank', ':rank'))
            ->andWhere($qb->expr()->eq('role.active', ':active'))
            ->setParameters(array(
                'program' => $program,
                'rank' => $rank,
                'active' => true
            ));
        // dump($qb->getDql());
        return $qb->getQuery()->getResult();
    }
}
