<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ResultatsSondagesQuizRepository extends EntityRepository
{
	public getResultsResponse($id)
    {
        $qb = $this->createQueryBuilder('rsq');
        $qb->select('rsq')
            ->join('rsq.sondages_quiz_questionnaire_infos','sqi')
            ->where('sqi = : id')
            ->setParameter('id',$id)
        return $qb->getQuery()->getResult();
    }
}
