<?php

namespace AdminBundle\Repository;

class LoginPortalSlideRepository extends \Doctrine\ORM\EntityRepository
{
    public function retrieveNumberOfOtherSlideUsingImage($login_portal_data, $current_slide)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(login_portal_slide)')
            ->from('AdminBundle:LoginPortalSlide', 'login_portal_slide')
            ->join('login_portal_slide.login_portal_data', 'login_portal_data')
            ->where($qb->expr()->neq('login_portal_slide', ':current_login_portal_slide'))
            ->andWhere($qb->expr()->eq('login_portal_slide.image', ':current_login_portal_slide_image'))
            ->andWhere($qb->expr()->eq('login_portal_slide.login_portal_data', ':login_portal_data'))
            ->setParameter('current_login_portal_slide', $current_slide)
            ->setParameter('current_login_portal_slide_image', $current_slide->getImage())
            ->setParameter('login_portal_data', $login_portal_data);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
