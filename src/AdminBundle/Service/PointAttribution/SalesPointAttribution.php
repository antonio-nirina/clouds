<?php

namespace AdminBundle\Service\PointAttribution;

use AdminBundle\Entity\ProgramUserClassmentProgression;
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
                            ->setPoints(($ca * $gain) / 100)
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
                                ->setPoints(($ca * $gain) / 100)
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

    public function updateUserClassmentPerformance(Sales $sales)
    {
        if (!$sales->getPerformanceAttributed()) {
            $program_user = $sales->getProgramUser();
            $date = $sales->getDate();
            $date_from = $sales->getDateFrom();
            $date_to = $sales->getDateTo();

            if ($date) {
                $month = date_format($date, "m");
                $year = date_format($date, "Y");
            } elseif ($date_from) {
                $month = date_format($date_from, "m");
                $year = date_format($date_from, "Y");
            } else {
                $month = date_format($date_to, "m");
                $year = date_format($date_to, "Y");
            }

            $classment_progression = $this->getClassmentProgression($program_user, $month, $year);

            if (empty($classment_progression)) {
                $classment_progression = $this->setNewClassmentProgression($program_user, $month, $year);
            } else {
                $classment_progression = $classment_progression[0];
            }

            $ca = $sales->getCa();
            $current_ca = ($classment_progression->getCurrentCa())?$classment_progression->getCurrentCa(): 0;
            $new_ca = $current_ca + $ca;
            $classment_progression->setCurrentCa($new_ca);
            if ($previous_ca = $classment_progression->getPreviousCa()) {
                $progression = ($new_ca/($previous_ca/100))-100;//methode de calcul venant du cube
                if ($progression >0) {
                    $classment_progression->setProgression($progression);
                }
            }

            $sales->setPerformanceAttributed(true);
            $this->em->flush();

            $this->adjustClassment($program_user->getRole(), $month, $year); //à faire systemtiquement ou par mois ?
        }

        return $sales;
    }

    public function adjustClassment($role, $month, $year)// classement par rôle
    {
        $user_arranged = $this->em->getRepository("AdminBundle:ProgramUser")
                                    ->findArrangedUsersByRole($role, $month, $year);
        foreach ($user_arranged as $k => $user) {
            $classment_progression = $user->getClassmentProgression();
            $classment_progression[0]->setClassment($k+1);
        }

        $this->em->flush();
        return;
    }

    public function getClassmentProgression($program_user, $month, $year)
    {
        return $this->em->getRepository("AdminBundle:ProgramUserClassmentProgression")->findBy(
            array(
                    'program_user' => $program_user,
                    'month' => $month,
                    'year' => $year
            )
        );
    }

    public function getPreviousClassmentProgression($program_user, $month, $year)
    {
        $previous_date = \DateTime::createFromFormat('m-Y', ($month-1).'-'.$year);
        $previous_month = date_format($previous_date, "m");
        $previous_year = date_format($previous_date, 'Y');

        return $this->em->getRepository("AdminBundle:ProgramUserClassmentProgression")->findBy(
            array(
                    'program_user' => $program_user,
                    'month' => $previous_month,
                    'year' => $previous_year
            )
        );
    }

    public function setNewClassmentProgression($program_user, $month, $year)//to launch by cron every month or manually?
    {
        $previous_classment_progression = $this->getPreviousClassmentProgression($program_user, $month, $year);
        $classment_progression = new ProgramUserClassmentProgression();
        $classment_progression  ->setMonth($month)
                                ->setYear($year)
                                ->setProgramUser($program_user);
        
        if ($previous_classment_progression) {
            $previous_cl_pr = $previous_classment_progression[0];
            $classment_progression  ->setClassment($previous_cl_pr->getClassment())
                                    ->setPreviousCa($previous_cl_pr->getCurrentCa());
        }
        $this->em->persist($classment_progression);
        $this->em->flush();

        return $classment_progression;
    }

    public function attributedByPerformance($program, $month, $year)//to launch by cron every month or manually?
    {
        $performance_type_1 = $this->em->getRepository('AdminBundle:PointAttributionType')->findBy(
            array("point_type_name" => 'performance_1')
        );
        $performance_type_2 = $this->em->getRepository('AdminBundle:PointAttributionType')->findBy(
            array("point_type_name" => 'performance_2')
        );

        $performance_point_setting1 = $this->getPerformancePointSetting($performance_type_1, $program);
        $date = date_format(\DateTime::createFromFormat("m-Y", $month."-".$year), "F Y");

        if ($performance_point_setting1[0]->getStatus() == "on") {//par progression
            foreach ($performance_point_setting1 as $point_setting) {
                $min = $point_setting->getMinValue();
                $max = $point_setting->getMaxValue();
                $gain = $point_setting->getGain();

                if (isset($min) && isset($max) && isset($gain)) {
                    $program_users = $this->em->getRepository('AdminBundle:ProgramUser')
                    ->findProgressionByProgramByMaxMinValue($program, $max, $min, $month, $year);
                    
                    if ($program_users) {
                        foreach ($program_users as $program_user) {
                            $user_point = new UserPoint();
                            $user_point->setProgramUser($program_user)
                                ->setDate(new \DateTime())
                                ->setAmount($gain)
                                ->setMotif("progression ".$date);
                            $this->em->persist($user_point);
                        }
                        
                        $this->em->flush();
                    }
                }
            }
        }

        $performance_point_setting2 = $this->getPerformancePointSetting($performance_type_2, $program);
        if ($performance_point_setting2[0]->getStatus() == "on") {//par classement
            foreach ($performance_point_setting2 as $point_setting) {
                $min = $point_setting->getMinValue();
                $max = $point_setting->getMaxValue();
                $gain = $point_setting->getGain();
                if (isset($min) && isset($max) && isset($gain)) {
                    $program_users = $this->em->getRepository('AdminBundle:ProgramUser')
                    ->findClassmentByProgramByMaxMinValue($program, $max, $min, $month, $year);

                    if ($program_users) {
                        foreach ($program_users as $program_user) {
                            $user_point = new UserPoint();
                            $user_point->setProgramUser($program_user)
                                ->setDate(new \DateTime())
                                ->setAmount($gain)
                                ->setMotif("classement ".$date);
                            $this->em->persist($user_point);
                        }
                        
                        $this->em->flush();
                    }
                }
            }
        }
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
                if (isset($min) && isset($max) && isset($gain) && $ca >= $min && $ca <= $max) {
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
                    if (isset($min) && isset($max) && isset($gain) && $ca >= $min && $ca <= $max) {
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

    public function getPerformancePointSetting($type, $program)
    {
        return $this->em
                    ->getRepository('AdminBundle:PointAttributionSetting')
                    ->findBy(
                        array(
                            "type" => $type,
                            "program" => $program,
                        )
                    );
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
