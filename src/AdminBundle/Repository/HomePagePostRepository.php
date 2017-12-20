<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HomePagePostRepository extends EntityRepository
{
    public function findByProgramAndPostTypeOrdered($program, $post_type)
    {
        $qb = $this->createQueryBuilder('post');
        $qb->where($qb->expr()->eq('post.program', ':program'))
            ->andWhere($qb->expr()->eq('post.post_type', ':post_type'))
            ->orderBy('post.created_at', 'DESC')
            ->setParameter('program', $program)
            ->setParameter('post_type', $post_type);

        return $qb->getQuery()->getResult();
    }
}
