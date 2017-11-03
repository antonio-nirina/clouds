<?php

namespace AdminBundle\Repository;

class SiteFormFieldSettingRepository extends \Doctrine\ORM\EntityRepository
{
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
}
