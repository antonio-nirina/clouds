<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProgramUserRepository extends EntityRepository
{
    public function findOneByEmailAndProgram($email, $program)
    {
        $qb = $this->createQueryBuilder('program_user');
        $qb->addSelect('app_user')
            ->addSelect('program')
            ->join('program_user.app_user', 'app_user')
            ->join('program_user.program', 'program')
            ->where($qb->expr()->eq('app_user.email', ':email'))
            ->andWhere($qb->expr()->eq('program_user.program', ':program'))
            ->setParameter('email', $email)
            ->setParameter('program', $program);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByNameAndLastName($name, $firstname, $program)
    {
        $qb = $this->createQueryBuilder('pu');
        $qb->select('pu')
            ->join('pu.program', 'prg')
            ->join('pu.app_user', 'app_user')
            ->where($qb->expr()->eq('app_user.name', ':name'))
            ->andWhere($qb->expr()->eq('app_user.firstname', ':firstname'))
            ->andWhere($qb->expr()->eq('pu.program', ':program'))
            ->setParameters(array(
                                "name" => $name,
                                "firstname" => $firstname,
                                "program" => $program
                                 ));

        return $qb->getQuery()->getResult();
    }

    public function findArrangedUsersByRole($role, $start_date)
    {
        $qb = $this->createQueryBuilder('pu');
        $qb->addSelect('cl_pr')
            ->join('pu.classment_progression', 'cl_pr')
            ->where($qb->expr()->eq('pu.role', ':role'))
            ->andWhere($qb->expr()->eq('cl_pr.start_date', ':start_date'))
            ->andWhere($qb->expr()->isNotNull('cl_pr.current_ca'))
            ->orderBy('cl_pr.current_ca', 'DESC')
            ->setParameters(array(
                                "role" => $role,
                                "start_date" => $start_date
                                ));
        return $qb->getQuery()->getResult();
    }

    public function findProgressionByProgramByMaxMinValue($program, $max, $min, $date)
    {
        $qb = $this->createQueryBuilder('pu');
        $qb->addSelect('cl_pr')
            ->join('pu.classment_progression', 'cl_pr')
            ->where($qb->expr()->eq('pu.program', ':program'))
            ->andWhere($qb->expr()->lte('cl_pr.progression', ':max'))
            ->andWhere($qb->expr()->gte('cl_pr.progression', ':min'))
            ->andWhere($qb->expr()->lte('cl_pr.start_date', ':date'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('cl_pr.end_date'),
                        $qb->expr()->gte('cl_pr.end_date', ':date')
                    ),
                    $qb->expr()->isNull('cl_pr.end_date')
                )
            )
            ->setParameter("program", $program)
            ->setParameter("max", $max)
            ->setParameter("min", $min)
            ->setParameter("date", $date);

        return $qb->getQuery()->getResult();
    }

    public function findClassmentByProgramByMaxMinValue($program, $max, $min, $date)
    {
        $qb = $this->createQueryBuilder('pu');
        $qb->addSelect('cl_pr')
            ->join('pu.classment_progression', 'cl_pr')
            ->where($qb->expr()->eq('pu.program', ':program'))
            ->andWhere($qb->expr()->lte('cl_pr.classment', ':max'))
            ->andWhere($qb->expr()->gte('cl_pr.classment', ':min'))
            ->andWhere($qb->expr()->lte('cl_pr.start_date', ':date'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('cl_pr.end_date'),
                        $qb->expr()->gte('cl_pr.end_date', ':date')
                    ),
                    $qb->expr()->isNull('cl_pr.end_date')
                )
            )
            ->setParameter("program", $program)
            ->setParameter("max", $max)
            ->setParameter("min", $min)
            ->setParameter("date", $date);

        return $qb->getQuery()->getResult();
    }
}
