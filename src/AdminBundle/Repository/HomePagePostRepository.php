<?php

namespace AdminBundle\Repository;

use AdminBundle\Component\Post\PostType;
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

    public function findPublishedNewsPost($program)
    {
        $qb = $this->createQueryBuilder('post');
        $qb->join('post.news_post', 'news_post')
            ->andWhere($qb->expr()->eq('post.program', ':program'))
            ->andWhere($qb->expr()->eq('news_post.published_state', ':published_state'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('post.post_type', ':news_post_type'),
                    $qb->expr()->eq('post.post_type', ':welcoming_news_post_type')
                )
            )
            ->setParameters(array(
                'program' => $program,
                'published_state' => true,
                'news_post_type' =>  PostType::NEWS_POST,
                'welcoming_news_post_type' => PostType::WELCOMING_NEWS_POST
            ));

        return $qb->getQuery()->getResult();
    }

}
