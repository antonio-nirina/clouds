<?php

namespace AdminBundle\Repository;

class NewsPostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllByProgram($program, $post_type, $archived_state = false)
    {
        $qb = $this->createQueryBuilder('news_post');
        $qb->join('news_post.home_page_post', 'home_page_post')
            ->orderBy('home_page_post.created_at', 'DESC')
            ->where($qb->expr()->eq('home_page_post.program', ':program'))
            ->andWhere($qb->expr()->eq('news_post.archived_state', ':archived_state'))
            ->andWhere($qb->expr()->eq('home_page_post.post_type', ':post_type'))
            ->setParameter('program', $program)
            ->setParameter('post_type', $post_type)
            ->setParameter('archived_state', $archived_state);

        return $qb->getQuery()->getResult();
    }

    public function findOneByIdAndProgram($id, $program)
    {
        $qb = $this->createQueryBuilder('news_post');
        $qb->join('news_post.home_page_post', 'home_page_post')
            ->where($qb->expr()->eq('home_page_post.program', ':program'))
            ->andWhere($qb->expr()->eq('news_post.id', ':id'))
            ->setParameter('program', $program)
            ->setParameter(':id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
