<?php

namespace AdminBundle\Service\PointAttribution;

use AdminBundle\Entity\Sales;
use AdminBundle\Entity\UserPoint;
use Doctrine\ORM\EntityManager;

class SalesPointAttribution
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function attributedByRank(Sales $sales)
    {
        if (!$sales->getRankAttributed()) {
            $program_user = $sales->getProgramUser();
            $role = $program_user->getRole();
            $rank = $role->getRank();
            $ca = $sales->getCa();

            $gain = $role->getGain();
            if ($gain) {//for current user
                $user_point = new UserPoint();
                $user_point->setProgramUser($program_user)
                            ->setDate(new \DateTime())
                            ->setAmount(($ca * $gain) / 100)
                            // ->setPoint("")
                            ->setMotif("rang")
                            ->setReference("sales_".$sales->getId());
                $this->em->persist($user_point);
            }

            $higher_role = $this->em->getRepository('AdminBundle:Role')->findHigherRank(
                $program_user->getProgram(),
                $rank
            );
            if ($higher_role) {
                foreach ($higher_role as $hr) {
                    if ($gain = $hr->getGain()) {
                        $higher_users = $this->em->getRepository('AdminBundle:ProgramUser')->findBy(
                            array(
                                'role' => $hr
                            )
                        );
                        foreach ($higher_users as $user) {//for superior users
                            $user_point = new UserPoint();
                            $user_point->setProgramUser($user)
                                ->setDate(new \DateTime())
                                ->setAmount(($ca * $gain) / 100)
                                // ->setPoint("")
                                ->setMotif("rang")
                                ->setReference("sales_".$sales->getId());
                            $this->em->persist($user_point);
                        }
                    }
                }
            }
            
            $sales->setRankAttributed(true);
            $this->em->flush();
        }

        return $sales;
    }

    public function attributedByPeriod(Sales $sales, $user_point)
    {
        if (!$sales->getPeriodAttributed()) {
            $product_group = $sales->getProductGroup();
            $product_group = ($product_group)?$product_group:1;
            $date = ($sales->getDate())?date_format($sales->getDate(), 'F'):$sales->getDate();
            $date_from = ($sales->getDateFrom())?date_format($sales->getDateFrom(), 'F'):$sales->getDateFrom();
            $date_to = ($sales->getDateTo())?date_format($sales->getDateTo(), 'F'):$sales->getDateTo();

            $period_point_setting = $this->em->getRepository("AdminBundle:PeriodPointSetting")->findBy(
                array(
                        'product_group' => $product_group
                    )
            );
            if (!$period_point_setting && $product_group != 1) {
                $period_point_setting = $this->em->getRepository("AdminBundle:PeriodPointSetting")
                ->findBy(
                    array(
                                'product_group' => 1
                            )
                );
            }
            $arr_point_setting = $period_point_setting[0]->getGain();
            $month = ($date)?$date:(($date_from)?$date_from:$date_to);
            $gain = $arr_point_setting[$month];

            if ($gain) {
                $current_point = $user_point->getPoints();
                $user_point ->setPoint(($current_point * $gain) /100);
            }
        }

        $sales->setPeriodAttributed(true);
        $this->em->flush();

        return $sales;
    }

    public function attributedByPerformance(Sales $sales)
    {
        $sales->setPerformanceAttributed(true);
        return $sales;
    }

    public function attributedByProduct(Sales $sales)
    {
        if (!$sales->getPeriodAttributed()) {
            $product_group = $sales->getProductGroup();
            $product_group = ($product_group)?$product_group:1;
        }
        
        $sales->setProductAttributed(true);
        return $sales;
    }
}
