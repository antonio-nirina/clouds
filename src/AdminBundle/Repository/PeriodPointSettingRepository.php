<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PeriodPointSettingRepository extends EntityRepository
{
    public function findMaxProductGroup($program)
    {
        $qb = $this->createQueryBuilder('pps');
        $qb->select('pps.product_group')
            ->join('pps.program', 'program')
            ->where($qb->expr()->eq('program', ':program'))
            ->groupBy('pps.product_group')
            ->orderBy('pps.product_group', 'DESC')
            ->setParameter('program', $program);
        // dump($qb->getQuery());die;

        return $qb->getQuery()->getArrayResult();
    }
}
