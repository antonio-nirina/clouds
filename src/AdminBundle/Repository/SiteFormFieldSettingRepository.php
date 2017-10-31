<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SiteFormFieldSettingRepository extends EntityRepository
{
    public function findAllInRow($row, $form_setting_id)
    {
        $qb = $this->createQueryBuilder('ff');
        $qb->select()
            ->join('ff.site_form_setting', 'sfs')
            ->where($qb->expr()->eq('ff.in_row', ':row'))
            ->andWhere($qb->expr()->eq('sfs.id', ':form_setting_id'))
            ->setParameters(['row' => $row, 'form_setting_id' => $form_setting_id]);

        return $qb->getQuery()->getResult();
    }
}
