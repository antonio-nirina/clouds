<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SondagesQuizReponsesRepository extends EntityRepository
{
    public function findBySondagesQuizReponses($sondages_quiz_questions)
    {
        $qb = $this->createQueryBuilder('sr');
        $qb ->where($qb->expr()->eq('sr.sondages_quiz_questions', ':sondages_quiz_questions'))
            ->setParameters(
                array(
                'sondages_quiz_questions' => $sondages_quiz_questions->getId(),
                )
            );
        // dump($qb->getDql());
        return $qb->getQuery()->getResult();
    }

    public function getReponseByQuestion($id)
    {
        $qb = $this->createQueryBuilder('sr');
        $qb->select('sr')
            ->join('sr.sondages_quiz_questions','sq')
            ->where('sq.id = :id')
            ->setParameter('id',$id);
        return $qb->getQuery()->getResult();
    }
}
