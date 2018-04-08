<?php

namespace AdminBundle\Manager;

use AdminBundle\Entity\PointAttributionSetting;
use AdminBundle\Entity\Program;
use Doctrine\ORM\EntityManager;

class PointAttributionManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setAllPerformancePoint(Program $program)
    {
        $range = array('A',"B","C");
        $type_performance_1 = $this->getTypePerformance1();
        $point_ca_range = $this->getCaRange($program);

        if (empty($point_ca_range)) {
            foreach ($range as $r) {
                $point = new PointAttributionSetting();
                $point->setProgram($program)
                    ->setType($type_performance_1[0])
                    ->setName('tranche '.$r)
                    ->setStatus('off');
                $this->em->persist($point);
            }
        }

        $type_performance_2 = $this->getTypePerformance2();
        $point_classment_range = $this->getClassmentRange($program);

        if (empty($point_classment_range)) {
            foreach ($range as $r) {
                $point = new PointAttributionSetting();
                $point->setProgram($program)
                    ->setType($type_performance_2[0])
                    ->setName('tranche '.$r)
                    ->setStatus('off');
                $this->em->persist($point);
            }
        }

        if (empty($point_classment_range) || empty($point_ca_range)) {
            $this->em->flush();
        }
        
        return ;
    }

    public function setPerformance1($program)
    {
        $this->setAllPerformancePoint($program);
        $ca_range = $this->getCaRange($program);

        foreach ($program->getPointAttributionSetting() as $cr) {
            $program->getPointAttributionSetting()->removeElement($cr);
        }
        foreach ($ca_range as $cr) {
            $program->getPointAttributionSetting()->add($cr);
        }

        return $program;
    }

    public function setPerformance2($program)
    {
        $this->setAllPerformancePoint($program);
        $point_classment_range = $this->getClassmentRange($program);

        foreach ($program->getPointAttributionSetting() as $cr) {
            $program->getPointAttributionSetting()->removeElement($cr);
        }
        foreach ($point_classment_range as $pcr) {
            $program->getPointAttributionSetting()->add($pcr);
        }

        return $program;
    }

    public function getTypePerformance1()
    {
        return $this->em->getRepository('AdminBundle:PointAttributionType')
            ->findBy(
                array('point_type_name' => "performance_1")
            );
    }

    public function getCaRange($program)
    {
        $type_performance_1 = $this->getTypePerformance1();

        $point_ca_range = $this->em->getRepository('AdminBundle:PointAttributionSetting')->findBy(
            array(
                'program' => $program,
                'type' => $type_performance_1
            )
        );

        return $point_ca_range;
    }

    public function getTypePerformance2()
    {
        return $this->em->getRepository('AdminBundle:PointAttributionType')
            ->findBy(
                array('point_type_name' => "performance_2")
            );
    }

    public function getClassmentRange($program)
    {
        $type_performance_2 = $this->getTypePerformance2();

        $point_classment_range = $this->em->getRepository('AdminBundle:PointAttributionSetting')->findBy(
            array(
                'program' => $program,
                'type' => $type_performance_2
            )
        );

        return $point_classment_range;
    }
}
