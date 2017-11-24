<?php

namespace AdminBundle\Repository;

class LoginPortalDataRepository extends \Doctrine\ORM\EntityRepository
{
    public function retrieveMaxSlideOrderByLoginPortalData($login_portal_data)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('MAX(login_portal_slide.slide_order)')
            ->from('AdminBundle:LoginPortalData', 'login_portal_data')
            ->join('login_portal_data.login_portal_slides', 'login_portal_slide')
            ->where($qb->expr()->eq('login_portal_data', ':login_portal_data'))
            ->setParameter('login_portal_data', $login_portal_data);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
