<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SondagesQuizQuestionnaireInfosRepository extends EntityRepository
{
    public function findBySondagesQuiz($sondages_quiz)
    {
        $qb = $this->createQueryBuilder('sqi');
        $qb ->where($qb->expr()->eq('sqi.sondages_quiz', ':sondages_quiz'))
            ->setParameters(
                array(
                'sondages_quiz' => $sondages_quiz->getId(),
                )
            );
        // dump($qb->getDql());
        return $qb->getQuery()->getResult();
    }

    public function findQuizByProgram($program)
    {
        $qb = $this->createQueryBuilder('sqqi');
        $qb->join('sqqi.sondages_quiz', 'sq')
            ->where($qb->expr()->eq('sq.program', ':program'))
            ->andWhere($qb->expr()->eq('sqqi.est_publier', ':published_state'))
            ->andWhere($qb->expr()->eq('sqqi.type_sondages_quiz', ':type'))
            ->setParameters(
                array(
                'program' => $program,
                'published_state' => true,
                'type' => 2,
                )
            );

        return $qb->getQuery()->getResult();
    }

    /**
     *
     */
    public function getStatusClotureArchived()
    {
        $qb = $this->createQueryBuilder('sqi');
        $qb->where($qb->expr()->eq('sqqi.est_archived', ':archived'))
            ->andWhere($qb->expr()->eq('sqqi.est_cloture', ':cloture_state'))
            ->setParameters(
                array(
                "archived"=> true,
                "cloture_state" => true
                )
            );

        return $qb->getQuery()->getResult();

    }

    /**
     *
     */
    public function getStatusPublieArchived()
    {
        $qb = $this->createQueryBuilder('sqi');
        $qb->where($qb->expr()->eq('sqqi.est_archived', ':archived'))
            ->andWhere($qb->expr()->eq('sqqi.est_publier', ':published_state'))
            ->setParameters(
                array(
                "archived"=> true,
                "published_state" => true
                )
            );
        return $qb->getQuery()->getResult();
    }
    public function getStatusAttenteArchived()
    {
        $qb = $this->createQueryBuilder('sqi');
        $qb->where($qb->expr()->eq('sqqi.est_archived', ':archived'))
            ->andWhere($qb->expr()->eq('sqqi.est_cloture', ':cloture_state'))
            ->andWhere($qb->expr()->eq('sqqi.est_publier', ':published_state'))
            ->setParameters(
                array(
                "archived"=> true,
                "cloture_state" => false,
                "published_state"=>false
                )
            );
        return $qb->getQuery()->getResult();
    }


}
