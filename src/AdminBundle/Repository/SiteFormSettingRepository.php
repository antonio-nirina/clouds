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

    public function findByProgramAndTypeWithFieldWithLevel($program, $form_type)
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
            ->andWhere($qb->expr()->isNotNull('field.level'))
            ->andWhere('field.level > 0')
            ->orderBy('field.field_order', 'ASC')
            ->setParameter('program', $program)
            ->setParameter('form_type', $form_type);
       // dump($qb->getQuery());die;

        return $qb->getQuery()->getSingleResult();
    }

    public function findByProgramAndTypeAndLevelWithField($program, $form_type, $level)
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
            // ->andWhere($qb->expr()->isNull('field.level'))
            ->andWhere($qb->expr()->eq('field.level', ':level'))
            ->orderBy('field.field_order', 'ASC')
            ->setParameter('program', $program)
            ->setParameter('form_type', $form_type)
            ->setParameter('level', $level);
       // dump($qb->getQuery());die;

        return $qb->getQuery()->getSingleResult();
    }

    public function findAllDefaultFields($program, $form_type)
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
            ->andWhere($qb->expr()->isNull('field.level'))
            ->orderBy('field.field_order', 'ASC')
            ->setParameter('program', $program)
            ->setParameter('form_type', $form_type);
       // dump($qb->getQuery());die;

        return $qb->getQuery()->getSingleResult();
    }

    public function findMaxLevel($program, $form_type)
    {
        $qb = $this->createQueryBuilder('site_form_setting');
        $qb->select('field.level')
            ->join('site_form_setting.site_form', 'site_form')
            ->join('site_form_setting.program', 'program')
            ->leftJoin('site_form_setting.site_form_field_settings', 'field')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('site_form.form_type', ':form_type'))
            ->andWhere($qb->expr()->isNotNull('field.level'))
            ->groupBy('field.level')
            ->orderBy('field.level', 'DESC')
            ->setParameter('program', $program)
            ->setParameter('form_type', $form_type);
       // dump($qb->getQuery());die;

        return $qb->getQuery()->getArrayResult();
    }
}
