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
            if (empty($period_point_setting) && $product_group != 1) {
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
                $user_point ->setPoints(($current_point * $gain) /100)
                            ->setMotif('produit, période');
                $sales->setPeriodAttributed(true);
                $this->em->flush();
            }
        }

        return $sales;
    }

    public function attributedByPerformance(Sales $sales)
    {
        $sales->setPerformanceAttributed(true);
        return $sales;
    }

    public function attributedByProduct(Sales $sales)
    {
        if (!$sales->getProductAttributed()) {
            $product_group = $sales->getProductGroup();
            $product_group = ($product_group)?$product_group:1;
            $program_user = $sales->getProgramUser();
            $program = $program_user->getProgram();
            $ca = $sales->getCa();

            $option_type_1 = $this->em->getRepository('AdminBundle:PointAttributionType')->findBy(
                array("point_type_name" => 'product-turnover-proportional')
            );
            $option_type_2 = $this->em->getRepository('AdminBundle:PointAttributionType')->findBy(
                array("point_type_name" => 'product-turnover-slice')
            );

            $product_point_setting1 = $this->getPointSetting($option_type_1, $product_group, $program);
            if (empty($product_point_setting1)) {//ca general sinon
                $product_group = 1;
                $product_point_setting1 = $this->getPointSetting($option_type_1, $product_group, $program);
            }

            if ($product_point_setting1[0]->getStatus() == "on") {
                $min = $product_point_setting1[0]->getMinValue();
                $max = $product_point_setting1[0]->getMaxValue();
                $gain = $product_point_setting1[0]->getGain();
                if ($min && $max && $gain && $ca >= $min && $ca <= $max) {
                    $user_point = new UserPoint();
                    $user_point->setProgramUser($program_user)
                        ->setDate(new \DateTime())
                        ->setPoints(($ca * $gain) /100)
                        ->setMotif("produit")
                        ->setReference("sales_".$sales->getId());
                    $this->em->persist($user_point);
                    $this->attributedByPeriod($sales, $user_point);
                    $sales->setProductAttributed(true);
                    $this->em->flush();
                }
            } else {
                $product_point_setting2 = $this->getPointSetting(
                    $option_type_2,
                    $product_group,
                    $program
                );

                foreach ($product_point_setting2 as $point_setting) {
                    $min = $point_setting->getMinValue();
                    $max = $point_setting->getMaxValue();
                    $gain = $point_setting->getGain();
                    if ($min && $max && $gain && $ca >= $min && $ca <= $max) {
                        $user_point = new UserPoint();
                        $user_point->setProgramUser($program_user)
                            ->setDate(new \DateTime())
                            ->setPoints(($ca * $gain) /100)
                            ->setMotif("produit")
                            ->setReference("sales_".$sales->getId());
                        $this->em->persist($user_point);
                        $this->attributedByPeriod($sales, $user_point);
                        $sales->setProductAttributed(true);
                        $this->em->flush();
                        break;
                    }
                }
            }
        }
        
        return $sales;
    }

    public function getPointSetting($type, $product_group, $program)
    {
        return $this->em
                    ->getRepository('AdminBundle:PointAttributionSetting')
                    ->findBy(
                        array(
                            "type" => $type,
                            "product_group" => $product_group,
                            "program" => $program,
                        )
                    );
    }
}
