<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PointAttributionSettingRepository extends EntityRepository
{
    public function retrieveAvailableProductGroupByProgramAndType($program, $point_attribution_type_name)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT pt_attr_setting.product_group')
            ->from('AdminBundle:PointAttributionSetting', 'pt_attr_setting')
            ->join('pt_attr_setting.program', 'program')
            ->join('pt_attr_setting.type', 'pt_attr_type')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('pt_attr_type.point_type_name', ':name'))
            ->orderBy('pt_attr_setting.product_group', 'ASC')
            ->setParameter('program', $program)
            ->setParameter('name', $point_attribution_type_name);

        return $qb->getQuery()->getScalarResult();
    }

    public function findByProgramAndTypeAndProductGroup($program, $type_name, $product_group)
    {
        $qb = $this->createQueryBuilder('pt_attr_setting');
        $qb->join('pt_attr_setting.program', 'program')
            ->join('pt_attr_setting.type', 'pt_attr_type')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('pt_attr_type.point_type_name', ':type_name'))
            ->andWhere($qb->expr()->eq('pt_attr_setting.product_group', ':product_group'))
            ->setParameter('program', $program)
            ->setParameter('type_name', $type_name)
            ->setParameter('product_group', $product_group);

        return $qb->getQuery()->getResult();
    }

    public function findByProgramAndTypeAndAfterProductGroup($program, $type_name, $product_group)
    {
        $qb = $this->createQueryBuilder('pt_attr_setting');
        $qb->join('pt_attr_setting.program', 'program')
            ->join('pt_attr_setting.type', 'pt_attr_type')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('pt_attr_type.point_type_name', ':type_name'))
            ->andWhere($qb->expr()->gt('pt_attr_setting.product_group', ':product_group'))
            ->setParameter('program', $program)
            ->setParameter('type_name', $type_name)
            ->setParameter('product_group', $product_group);

        return $qb->getQuery()->getResult();
    }

    public function retrieveMaxProductGroupByProgramAndType($program, $point_attribution_type_name)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('MAX(pt_attr_setting.product_group)')
            ->from('AdminBundle:PointAttributionSetting', 'pt_attr_setting')
            ->join('pt_attr_setting.program', 'program')
            ->join('pt_attr_setting.type', 'pt_attr_type')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('pt_attr_type.point_type_name', ':name'))
            ->setParameter('program', $program)
            ->setParameter('name', $point_attribution_type_name);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findOneByProgramAndTypeAndProductGroupAndName($program, $type_name, $product_group, $name)
    {
        $qb = $this->createQueryBuilder('pt_attr_setting');
        $qb->join('pt_attr_setting.program', 'program')
            ->join('pt_attr_setting.type', 'pt_attr_type')
            ->where($qb->expr()->eq('program', ':program'))
            ->andWhere($qb->expr()->eq('pt_attr_type.point_type_name', ':type_name'))
            ->andWhere($qb->expr()->eq('pt_attr_setting.product_group', ':product_group'))
            ->andWhere($qb->expr()->eq('pt_attr_setting.name', ':name'))
            ->setParameter('program', $program)
            ->setParameter('type_name', $type_name)
            ->setParameter('product_group', $product_group)
            ->setParameter('name', $name);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
