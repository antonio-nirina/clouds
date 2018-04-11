<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SondagesQuizQuestionsRepository extends EntityRepository
{
    public function findBySondagesQuizQuestionnaireInfos($sondages_quiz_questionnaire_infos)
    {
        $qb = $this->createQueryBuilder('sq');
        $qb ->where($qb->expr()->eq('sq.sondages_quiz_questionnaire_infos', ':sondages_quiz_questionnaire_infos'))
            ->setParameters(
                array(
                'sondages_quiz_questionnaire_infos' => $sondages_quiz_questionnaire_infos->getId(),
                )
            );
        // dump($qb->getDql());
        return $qb->getQuery()->getResult();
    }

}
