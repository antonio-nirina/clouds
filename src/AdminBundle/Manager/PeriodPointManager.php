<?php

namespace AdminBundle\Manager;

use AdminBundle\Entity\PeriodPointSetting;
use AdminBundle\Entity\Program;
use Doctrine\ORM\EntityManager;

class PeriodPointManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setAllPeriodPoint(Program $program)
    {
        $period_point = $program->getPeriodPointSetting();

        if (!(count($period_point) > 0)) {
            $this->newPeriodPointProduct($program);
        }

        return $program;
    }

    public function deletePeriodPointProduct(Program $program, $product_group)
    {
        $product_period_point = $this->em->getRepository('AdminBundle:PeriodPointSetting')->findBy(
            array(
                'program' => $program,
                'product_group' => $product_group
            )
        );

        if (count($product_period_point) > 0) {
            $this->em->remove($product_period_point[0]);
            $this->em->flush();
        }
    }

    public function newPeriodPointProduct(Program $program)
    {
        $period_point = $program->getPeriodPointSetting();

        if (count($period_point) > 0) {
            $last = $this->em->getRepository('AdminBundle:PeriodPointSetting')->findMaxProductGroup($program);
            $max = $last[0]['product_group'];
            $max++;
        } else {
            $max = 1;
        }

        $gain = array();
        for ($i=1; $i<=12; $i++) {
            $opt = date_format(\DateTime::createFromFormat('m', $i), 'F');
            $gain[$opt] = '';
        }

        $period_point_setting = new PeriodPointSetting();
        $period_point_setting->setProductGroup($max)
            ->setGain($gain)
            ->setProgram($program);
        $program->getPeriodPointSetting()->add($period_point_setting);
        $this->em->flush();

        return $program;
    }
}
