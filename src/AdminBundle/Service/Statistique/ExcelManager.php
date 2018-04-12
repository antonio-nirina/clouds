<?php
namespace AdminBundle\Service\Statistique;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Service\Statistique\Common;
use Mailjet\MailjetBundle\Client\MailjetClient ;



class ExcelManager
{
    private $container;
    private $mailjetClient;
    private $common;

    /**
     * ExcelManager constructor.
     * @param MailjetClient $mailjetClient
     * @param Common $common
     * @param ContainerInterface $container
     */
    public function __construct(MailjetClient  $mailjetClient, Common $common, ContainerInterface $container)
    {        
        $this->mailjetClient=$mailjetClient;
        $this->common = $common;
        $this->container = $container;
    }

    /**
     * @param null $id
     * @param string $status
     * @return mixed
     */
    public function generateExcel($id = null, $status = "")
    {        
        $result = $this->common->getOneCampagne($id)["email"];
        $res = [];
        if (empty($status) || $status == "tous") {
            $objPHPExcel = $this->allTraitement($result);
        } elseif (!empty($status) && $status == "delivred") {
            foreach ($result as  $del) {
                if ($del["etat"] == "sent" || $del["etat"] == "opened" || $del["etat"] == "clicked" ) {
                    $res[] = $del;
                } 
            }
            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        } elseif (!empty($status) && $status == "opened") {
            foreach ($result as  $del) {
                if ($del["etat"] == "opened" || $del["etat"] == "clicked") {
                    $res[] = $del;
                } 
            }            
            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        } elseif (!empty($status) && $status == "clicked") {
            foreach ($result as  $del) {
                if ($del["etat"] == "clicked" ) {
                    $res[] = $del;
                }
            }
            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        } elseif (!empty($status) && $status == "bounce") {
            foreach ($result as  $del) {
                if ($del["etat"] == "bounce" ) {
                    $res[] = $del;
                }
            } 
            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        } elseif (!empty($status) && $status == "spam") {
            foreach ($result as  $del) {
                if ($del["etat"] == "spam" ) {
                    $res[] = $del;
                } 
            }

            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        } elseif (!empty($status) && $status == "unsub") {
            foreach ($result as  $del) {
                if ($del["etat"] == "unsub" ) {
                    $res[] = $del;
                } 
            }
            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        } elseif (!empty($status) && $status == "blocked") {
            foreach ($result as  $del) {
                if ($del["etat"] == "blocked" ) {
                    $res[] = $del;
                } 
            }
            $data = !empty($res)?$res:"";
            $objPHPExcel = $this->allTraitement($data);
        }
       
        return $objPHPExcel;
    }


    /**
     * @param $data
     * @return mixed
     */
    protected function allTraitement($data)
    {
        $objPHPExcel = $this->container->get('phpexcel')->createPHPExcelObject();
        $objPHPExcel->getProperties()->setCreator('CloudRewards');
        $objPHPExcel->getProperties()->setLastModifiedBy('CloudRewards');
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Listing");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Listing");
        $objPHPExcel->getProperties()->setDescription("Listing for Office 2007 XLSX, generated using Symfony.");       
        $bordersarray = array(
                'borders'=>array(
                'top'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN), 
                'left'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN),
                'right'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN),
                'bottom'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN)
            )
        );
        $delivre = mb_strtoupper("Délivrés"); 
        $clique =  mb_strtoupper("Cliqués"); 
        $desabo = mb_strtoupper("Désabo"); 
        $bloque =  mb_strtoupper("Bloqués"); 
        
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'e-mail');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', $delivre);
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'OUVERTS');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', $clique);
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', $desabo);
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', $bloque);
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'SPAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'ERREURS');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($bordersarray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($bordersarray);
        $center = array('alignment'=>array('horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER));
            
        $left = array('alignment'=>array('horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_LEFT));

        $souligner = array('font' => array('underline' => \PHPExcel_Style_Font::UNDERLINE_DOUBLE)); 
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);

        $styleEmail = [
            "font" =>["bold" => true, "color" => ["rgb"=>"000000"],"size"=>12, "name"=>"Arial"]
        ];

        $styleDelivre = [
            "font" =>["bold" => true, "color" => ["rgb"=>"808080"],"size"=>12, "name"=>"Arial"]
        ];

         $styleOuverts = [
            "font" =>["bold" => true, "color" => ["rgb"=>"93D706"],"size"=>12, "name"=>"Arial"]
         ];

         $styleClique = [
            "font" =>["bold" => true, "color" => ["rgb"=>"14A400"],"size"=>12, "name"=>"Arial"]
         ];

         $styleDesabo = [
            "font" =>["bold" => true, "color" => ["rgb"=>"1CD7F9"],"size"=>12, "name"=>"Arial"]
         ];

         $styleBloque = [
            "font" =>["bold" => true, "color" => ["rgb"=>"000000"],"size"=>12, "name"=>"Arial"]
         ];

         $styleSpam = [
            "font" =>["bold" => true, "color" => ["rgb"=>"FC0007"],"size"=>12, "name"=>"Arial"]
         ];

         $styleErreurs = [
            "font" =>["bold" => true, "color" => ["rgb"=>"FEA500"],"size"=>12, "name"=>"Arial"]
         ];

         $objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray($styleEmail);
         $objPHPExcel->getActiveSheet()->getStyle("B1")->applyFromArray($styleDelivre);
         $objPHPExcel->getActiveSheet()->getStyle("C1")->applyFromArray($styleOuverts);
         $objPHPExcel->getActiveSheet()->getStyle("D1")->applyFromArray($styleClique);
         $objPHPExcel->getActiveSheet()->getStyle("E1")->applyFromArray($styleDesabo);
         $objPHPExcel->getActiveSheet()->getStyle("F1")->applyFromArray($styleBloque);
         $objPHPExcel->getActiveSheet()->getStyle("G1")->applyFromArray($styleSpam);
         $objPHPExcel->getActiveSheet()->getStyle("H1")->applyFromArray($styleErreurs);

         $row = 2;
         if (!empty($data)) {
             foreach ($data as $item) {
                 if ($item["etat"] == "opened") {
                     $objPHPExcel->setActiveSheetIndex(0)
                         ->setCellValue('A'.$row, $item["emails"]);
                     $objPHPExcel->getActiveSheet()->getStyle('C'.$row)->applyFromArray($styleOuverts);
                     $objPHPExcel->getActiveSheet()->getCell('C'.$row)->setValue("x");
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->applyFromArray(
                        array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                    );

                     $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($styleDelivre);
                     $objPHPExcel->getActiveSheet()->getCell('B'.$row)->setValue("x");
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->applyFromArray(
                        array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                    );
                    } elseif ($item["etat"] == "sent") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $item["emails"]);
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($styleDelivre);
                        $objPHPExcel->getActiveSheet()->getCell('B'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );
                } elseif ($item["etat"] == "clicked") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $item["emails"]);
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->applyFromArray($styleClique);
                        $objPHPExcel->getActiveSheet()->getCell('D'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );

                        $objPHPExcel->getActiveSheet()->getStyle('C'.$row)->applyFromArray($styleOuverts);
                        $objPHPExcel->getActiveSheet()->getCell('C'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );

                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($styleDelivre);
                        $objPHPExcel->getActiveSheet()->getCell('B'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );
                    } elseif ($item["etat"] == "unsub") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $item["emails"]);
                        $objPHPExcel->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleDesabo);
                        $objPHPExcel->getActiveSheet()->getCell('E'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );
                } elseif ($item["etat"] == "blocked") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $item["emails"]);
                        $objPHPExcel->getActiveSheet()->getStyle('F'.$row)->applyFromArray($styleBloque);
                        $objPHPExcel->getActiveSheet()->getCell('F'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );
                    } elseif ($item["etat"] == "spam") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $item["emails"]);
                        $objPHPExcel->getActiveSheet()->getStyle('G'.$row)->applyFromArray($styleSpam);
                        $objPHPExcel->getActiveSheet()->getCell('G'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('G'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );
                 } elseif ($item["etat"] == "bounce") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $item["emails"]);
                        $objPHPExcel->getActiveSheet()->getStyle('H'.$row)->applyFromArray($styleErreurs);
                        $objPHPExcel->getActiveSheet()->getCell('H'.$row)->setValue("x");
                        $objPHPExcel->getActiveSheet()->getStyle('H'.$row)->getAlignment()->applyFromArray(
                            array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                        );
                    }
                    $row++;
}

                return $objPHPExcel;
             
            } else {

                return $objPHPExcel;
}
        
    }
        
}