<?php

namespace AdminBundle\Repository;

class SiteFormSettingRepository extends \Doctrine\ORM\EntityRepository
{
    public function findWithFieldById($id)
    {
        $qb = $this->createQueryBuilder('site_form_setting');
        $qb->addSelect('site_form_field_settings')
            ->join('site_form_setting.site_form_field_setting', 'site_form_field_settings')
            ->where($qb->expr()->eq('site_form_setting.id', ':id'))
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByProgramAndType($program, $form_type)
    {
        $qb = $this->createQueryBuilder('site_form_setting');
        $qb->addSelect('site_form')
            ->addSelect('program')
            ->join('site_form_setting.site_form', 'site_form')
            ->join('site_form_setting.program', 'program')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('site_form.form_type', ':form_type'))
            ->setParameter('program', $program)
            ->setParameter('form_type', $form_type);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByProgramAndTypeWithField($program, $form_type)
    {
        $qb = $this->createQueryBuilder('site_form_setting');
        $qb->addSelect('site_form')
            ->addSelect('program')
            ->addSelect('field')
            ->join('site_form_setting.site_form', 'site_form')
            ->join('site_form_setting.program', 'program')
            ->leftJoin('site_form_setting.site_form_field_settings', 'field')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('site_form.form_type', ':form_type'))
            ->orderBy('field.field_order', 'ASC')
            ->setParameter('program', $program)
            ->setParameter('form_type', $form_type);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
