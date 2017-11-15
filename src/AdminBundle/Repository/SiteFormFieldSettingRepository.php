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

    public function findBySiteFormSettingAndSpecialIndex($site_form_setting, $special_index)
    {
        $qb = $this->createQueryBuilder('site_form_field_setting');
        $qb->addSelect('site_form_setting')
            ->join('site_form_field_setting.site_form_setting', 'site_form_setting')
            ->where($qb->expr()->eq('site_form_setting', ':site_form_setting'))
            ->andWhere($qb->expr()->eq('site_form_field_setting.special_field_index', ':special_field_index'))
            ->setParameter('site_form_setting', $site_form_setting)
            ->setParameter('special_field_index', $special_index);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findBySiteFormSettingAndLabel($site_form_setting, $label)
    {
        $qb = $this->createQueryBuilder('site_form_field_setting');
        $qb->addSelect('site_form_setting')
            ->join('site_form_field_setting.site_form_setting', 'site_form_setting')
            ->where($qb->expr()->eq('site_form_setting', ':site_form_setting'))
            ->andWhere($qb->expr()->eq('site_form_field_setting.label', ':label'))
            ->setParameter('site_form_setting', $site_form_setting)
            ->setParameter('label', $label);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findBySiteFormSettingAndId($site_form_setting, $field_id)
    {
        $qb = $this->createQueryBuilder('site_form_field_setting');
        $qb->addSelect('site_form_setting')
            ->join('site_form_field_setting.site_form_setting', 'site_form_setting')
            ->where($qb->expr()->eq('site_form_setting', ':site_form_setting'))
            ->andWhere($qb->expr()->eq('site_form_field_setting.id', ':id'))
            ->setParameter('site_form_setting', $site_form_setting)
            ->setParameter('id', $field_id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
