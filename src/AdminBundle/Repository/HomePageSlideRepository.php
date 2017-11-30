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

    public function retrieveNumberOfOtherSlideUsingImage($home_page_data, $current_slide)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(home_page_slide)')
            ->from('AdminBundle:HomePageSlide', 'home_page_slide')
            ->join('home_page_slide.home_page_data', 'home_page_data')
            ->where($qb->expr()->neq('home_page_slide', ':current_home_page_slide'))
            ->andWhere($qb->expr()->eq('home_page_slide.image', ':current_home_page_slide_image'))
            ->andWhere($qb->expr()->eq('home_page_slide.home_page_data', ':home_page_data'))
            ->setParameter('current_home_page_slide', $current_slide)
            ->setParameter('current_home_page_slide_image', $current_slide->getImage())
            ->setParameter('home_page_data', $home_page_data);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
