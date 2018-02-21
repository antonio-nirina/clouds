<?php
namespace AdminBundle\Repository;

use AdminBundle\Entity\Program;

/**
 * Repository class for UserPoint Entity
 */
class UserPointRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find all points with respective user data
     *
     * @param Program $program
     *
     * @return array
     */
    public function findAllWithUserDataByProgram(Program $program)
    {
        $qb = $this->createQueryBuilder('user_point');
        $qb->join('user_point.program_user', 'program_user')
            ->join('program_user.app_user', 'app_user')
            ->where($qb->expr()->eq('program_user.program', ':program'))
            ->setParameter('program', $program);

        return $qb->getQuery()->getResult();
    }
}
