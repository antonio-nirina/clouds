<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProgramUserClassmentProgressionRepository extends EntityRepository
{
    public function findCurrentClassmentProgression($program_user, $date = false)
    {
        $qb = $this->createQueryBuilder('cl_pr');
        $qb->addSelect('pu')
            ->join('cl_pr.program_user', 'pu')
            ->where($qb->expr()->eq('cl_pr.program_user', ':program_user'));
        if ($date) {
            $qb->andWhere($qb->expr()->lte('cl_pr.start_date', ':date'))
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('cl_pr.end_date'),
                            $qb->expr()->gte('cl_pr.end_date', ':date')
                        ),
                        $qb->expr()->isNull('cl_pr.end_date')
                    )
                );
            $qb->setParameter("date", $date);
        }

        $qb->orderBy('cl_pr.start_date', 'DESC');
        $qb->setParameter("program_user", $program_user)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findPreviousClassmentProgression($program_user, $start)
    {
        $qb = $this->createQueryBuilder('cl_pr');
        $qb->addSelect('pu')
            ->join('cl_pr.program_user', 'pu')
            ->where($qb->expr()->eq('cl_pr.program_user', ':program_user'))
            ->andWhere($qb->expr()->lt('cl_pr.start_date', ':start'))
            ->orderBy('cl_pr.start_date', 'DESC')
            ->setParameters(array(
                "program_user" => $program_user,
                "start" => $start
            ))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
