<?php

namespace AdminBundle\Service\PointAttribution;

use AdminBundle\Entity\ProgramUserClassmentProgression;
use AdminBundle\Entity\Sales;
use AdminBundle\Entity\UserPoint;
use Doctrine\ORM\EntityManager;

class SalesPointAttribution
{
    private $em;
    private $ratio;
    const RATIO_POINT_EURO = 0.04;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->ratio = self::RATIO_POINT_EURO;
    }

    public function attributedByRank(Sales $sales)
    {
        if (!$sales->getRankAttributed()) {
            $program_user = $sales->getProgramUser();
            $role = $program_user->getRole();
            $rank = $role->getRank();
            $ca = $sales->getCa();

            /*Seulement pour les rangs supérieurs*/
            $higher_role = $this->em->getRepository('AdminBundle:Role')->findHigherRank(
                $program_user->getProgram(),
                $rank
            );
            $attributed = false;
            if ($higher_role) {
                foreach ($higher_role as $hr) {
                    if ($gain = $hr->getGain()) {
                        $higher_users = $this->em->getRepository('AdminBundle:ProgramUser')->findBy(
                            array(
                                'role' => $hr
                            )
                        );
                        foreach ($higher_users as $user) {//for superior users
                            $attributed = true;
                            $user_point = new UserPoint();
                            $user_point->setProgramUser($user)
                                ->setDate(new \DateTime())
                                ->setPoints((($ca * $gain) / 100) / $this->ratio)
                                ->setMotif("rang")
                                ->setReference("sales_" . $sales->getId());
                            $this->em->persist($user_point);
                        }
                    }
                }
            }

            $sales->setRankAttributed($attributed);
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
                $user_point ->setPoints(($current_point * $gain) /100)//multiplicateur
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

            $date = ($date)?$date:$date_from;
            $classment_progression = $this->getCurrentClassmentProgression($program_user, $date);

            if (empty($classment_progression)) {//initialisation pour tous program_user
                $first_day = $date->modify('first day of this month');
                $this->setNewClassmentProgression($program_user->getProgram(), $first_day);
                $classment_progression = $this->getCurrentClassmentProgression($program_user, $date);
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

            //à faire systemtiquement ou par mois ?
            $this->adjustCurrentClassment($program_user->getRole(), $classment_progression->getStartDate());
        }

        return $sales;
    }

    public function adjustCurrentClassment($role, $start_date)// classement par rôle
    {
        $user_arranged = $this->em->getRepository("AdminBundle:ProgramUser")
            ->findArrangedUsersByRole($role, $start_date);
        foreach ($user_arranged as $k => $user) {
            $classment_progression = $user->getClassmentProgression();
            $classment_progression[0]->setClassment($k+1);
        }

        $this->em->flush();
        return;
    }

    public function getCurrentClassmentProgression($program_user, $date = false)
    {
        return $this->em->getRepository("AdminBundle:ProgramUserClassmentProgression")
            ->findCurrentClassmentProgression($program_user, $date);
    }

    public function getPreviousClassmentProgression($program_user, $start)
    {
        return $this->em->getRepository("AdminBundle:ProgramUserClassmentProgression")
            ->findPreviousClassmentProgression($program_user, $start);
    }

    public function setNewClassmentProgression($program, $start = false, $end = false)
    {
        //to launch by cron every month or manually?
        if ($start === false) {
            $date = new \DateTime();
            $start = $date->modify('first day of this month');
        }

        $users = $this->em->getRepository("AdminBundle:ProgramUser")->findBy(
            array("program" => $program)
        );

        foreach ($users as $program_user) {
            $previous_classment_progression = $this->getPreviousClassmentProgression($program_user, $start);
            $classment_progression = new ProgramUserClassmentProgression();
            $classment_progression  ->setStartDate($start)
                ->setProgramUser($program_user);

            if ($end) {
                $classment_progression->setEndDate($end);
            }

            if ($previous_classment_progression) {
                $classment_progression->setPreviousCa($previous_classment_progression->getCurrentCa());
            }
            $this->em->persist($classment_progression);
        }
        $this->em->flush();

        return $classment_progression;
    }

    public function closeClassmentProgression($program, $performance = false)
    {
        $date = new \DateTime();
        $users = $this->em->getRepository("AdminBundle:ProgramUser")->findBy(
            array("program" => $program)
        );

        foreach ($users as $program_user) {
            $classment_progression = $this->getCurrentClassmentProgression($program_user, $date);
            $classment_progression->setEndDate($date);
            if ($performance) {
                $classment_progression->setIsPrevious(true);
            }
        }
        $this->em->flush();

        if ($performance) {
            $this->attributedByPerformance($program);
        }

        $this->setNewClassmentProgression($program, $date);
    }

    public function attributedByPerformance($program)
    {
        //to launch by cron every month or manually?
        $date = new \DateTime();
        $performance_type_1 = $this->em->getRepository('AdminBundle:PointAttributionType')->findBy(
            array("point_type_name" => 'performance_1')
        );
        $performance_type_2 = $this->em->getRepository('AdminBundle:PointAttributionType')->findBy(
            array("point_type_name" => 'performance_2')
        );

        $performance_point_setting1 = $this->getPerformancePointSetting($performance_type_1, $program);
        // $date = date_format(\DateTime::createFromFormat("m-Y", $month."-".$year), "F Y");

        if ($performance_point_setting1[0]->getStatus() == "on") {//par progression
            foreach ($performance_point_setting1 as $point_setting) {
                $min = $point_setting->getMinValue();
                $max = $point_setting->getMaxValue();
                $gain = $point_setting->getGain();

                if (isset($min) && isset($max) && isset($gain)) {
                    $program_users = $this->em->getRepository('AdminBundle:ProgramUser')
                        ->findProgressionByProgramByMaxMinValue($program, $max, $min, $date);

                    if ($program_users) {
                        foreach ($program_users as $program_user) {
                            $user_point = new UserPoint();
                            $user_point->setProgramUser($program_user)
                                ->setDate(new \DateTime())
                                ->setPoints($gain / $this->ratio)
                                ->setMotif("progression " . date_format($date, "d-m-Y"));
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
                        ->findClassmentByProgramByMaxMinValue($program, $max, $min, $date);

                    if ($program_users) {
                        foreach ($program_users as $program_user) {
                            $user_point = new UserPoint();
                            $user_point->setProgramUser($program_user)
                                ->setDate(new \DateTime())
                                ->setPoints($gain / $this->ratio)
                                ->setMotif("classement " . date_format($date, "d-m-Y"));
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
                        ->setPoints((($ca * $gain) /100) / $this->ratio)
                        ->setMotif("produit")
                        ->setReference("sales_" . $sales->getId());
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
                            ->setPoints((($ca * $gain) /100) / $this->ratio)
                            ->setMotif("produit")
                            ->setReference("sales_" . $sales->getId());
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
