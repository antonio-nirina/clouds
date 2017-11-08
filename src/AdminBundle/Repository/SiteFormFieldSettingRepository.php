<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SiteFormFieldSettingRepository extends EntityRepository
{
    public function findAllInRow($row, $form_setting_id, $level)
    {
        $qb = $this->createQueryBuilder('ff');
        $qb->select()
            ->join('ff.site_form_setting', 'sfs')
            ->where($qb->expr()->eq('ff.in_row', ':row'))
            ->andWhere($qb->expr()->eq('sfs.id', ':form_setting_id'))
            ->andWhere($qb->expr()->eq('ff.level', ':level'))
            ->orderBy('ff.field_order', 'ASC')
            ->setParameters([
                'row' => $row,
                'form_setting_id' => $form_setting_id,
                'level' => $level
            ]);

        return $qb->getQuery()->getResult();
    }

    public function findLastOrder($form_setting_id, $level)
    {
        $qb = $this->createQueryBuilder('ff');
        $qb->select('ff.field_order')
            ->join('ff.site_form_setting', 'sfs')
            ->where($qb->expr()->eq('sfs.id', ':form_setting_id'))
            ->andWhere($qb->expr()->eq('ff.level', ':level'))
            ->orderBy('ff.field_order', 'DESC')
            ->groupBy('ff.field_order')
            ->setMaxResults(1)
            ->setParameters([
                'form_setting_id' => $form_setting_id,
                'level' => $level
            ]);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
