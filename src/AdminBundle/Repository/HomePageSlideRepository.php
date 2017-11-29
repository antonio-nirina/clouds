<?php

namespace AdminBundle\Repository;

class HomePageSlideRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByHomePageDataOrdered($home_page_data)
    {
        $qb = $this->createQueryBuilder('home_page_slide');
        $qb->where($qb->expr()->eq('home_page_slide.home_page_data', ':home_page_data'))
            ->orderBy('home_page_slide.slide_order', 'ASC')
            ->setParameter('home_page_data', $home_page_data);

        return $qb->getQuery()->getResult();
    }
}
