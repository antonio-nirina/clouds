<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SondagesQuizQuestionnaireInfosRepository extends EntityRepository
{
	public function findBySondagesQuiz($sondages_quiz)
    {
        $qb = $this->createQueryBuilder('sqi');
        $qb ->where($qb->expr()->eq('sqi.sondages_quiz', ':sondages_quiz'))
            ->setParameters(array(
                'sondages_quiz' => $sondages_quiz->getId(),
            ));
        // dump($qb->getDql());
        return $qb->getQuery()->getResult();
    }
}
