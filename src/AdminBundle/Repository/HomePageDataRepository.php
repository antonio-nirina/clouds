<?php

namespace AdminBundle\Repository;

class HomePageDataRepository extends \Doctrine\ORM\EntityRepository
{
    public function retrieveMaxSlideOrderByHomePageData($home_page_data)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('MAX(home_page_slide.slide_order)')
            ->from('AdminBundle:HomePageData', 'home_page_data')
            ->join('home_page_data.home_page_slides', 'home_page_slide')
            ->where($qb->expr()->eq('home_page_data', ':home_page_data'))
            ->setParameter('home_page_data', $home_page_data);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
