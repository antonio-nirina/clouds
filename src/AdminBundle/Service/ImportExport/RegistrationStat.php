<?php
namespace AdminBundle\Service\ImportExport;

use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManager;
use AdminBundle\Service\Statistique\Common;




class RegistrationStat
{
    private $php_excel;
    private $php_excel_object;
    private $em;
    private $current_row;
    private $current_col;
    private $filesystem;
    private $site_form_setting;

    const WRITER_TYPE = "CSV";
    const FILE_NAME_AND_EXT = "liste.csv";
    const DATE_TITLE = "Date";
    const EMAIL_TITLE = "Modèle d'e-mail";
    const OBJET = "Objet";
    const LISTE = "Liste de contacts";
    const EXPED = "Expéditeur";

    private $date;
    private $email;
    private $objet;
    private $expediteur;
    private $listeContact;

    private $container;

    private $common;

    private $title_list;
    private $title_row_index_list;
    private $header_row_index_list;

    public function __construct(
        PHPExcelFactory $factory,
        EntityManager $em,
        ContainerInterface $container,
        Filesystem $filesystem,
        Common $common
    ) {
        $this->php_excel = $factory;
        $this->php_excel_object = $this->php_excel->createPHPExcelObject();
        $this->em = $em;

        $this->current_row = 1; // 1-based index
        $this->current_col = 0;

        $this->date = [];
        $this->email= [];
        $this->objet = [];
        $this->expediteur = [];
        $this->listeContact =  [];

        $this->container = $container;
        $this->filesystem = $filesystem;
        $this->common = $common;

        $this->title_list = array();
        $this->title_row_index_list = array();
        $this->header_row_index_list = array();
    }

    public function setSiteFormSetting($site_form_setting)
    {
        $this->site_form_setting = $site_form_setting;
    }

    
    private function createObject()
    {
        $this->createDateInfoBlock();
        $this->createObjetInfoBlock();
        $this->createExpediteurInfoBlock();
        $this->createListeContactInfoBlock();
        $this->createModelEmailInfoBlock();
        return $this->php_excel_object;
    }

    private function create()
    {
        $this->createObject();
        $writer = $this->php_excel->createWriter($this->php_excel_object, self::WRITER_TYPE);

        return $writer;
    }

    private function createDateInfoElements()
    {
        $field = $this->common->getListCampaign($this->site_form_setting);
         if (!empty($field)) {
            $res = $field["campaigns"]["CreatedAt"];
            $date = new \DateTime($res);
            $var = $date->format("d/m/Y");
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $var);
                $this->current_row++;
                array_push($this->date, $var);
            
        }

        return;
    }

    private function createObjetInfoElements()
    {
        $field = $this->common->getListCampaign($this->site_form_setting);
         if (!empty($field)) {
            $res = $field["campaigns"]["Subject"];
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $res);
                $this->current_row++;
                array_push($this->objet, $res);
            }
        
        return;
    }

    private function createExpediteurInfoElements()
    {
        $field = $this->common->getListCampaign($this->site_form_setting);
         if (!empty($field)) {
            $res = $field["campaigns"]["FromEmail"];
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $res);
                $this->current_row++;
                array_push($this->expediteur, $res);
            }
        
        return;
    }

    private function createListInfoElements()
    {
        $field = $this->common->getOneCampagne($this->site_form_setting);
         if (!empty($field)) {
            $res = $field["listContact"];
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $res);
                $this->current_row++;
                array_push($this->listeContact, $res);
            }
        
        return;
    }

    private function createEmailInfoElements() //modele d'email
    {
        $field = $this->common->getListCampaign($this->site_form_setting);
         if (!empty($field)) {
            $name = $field["template"];
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $name);
                $this->current_row++;
                array_push($this->email, $name);
        }
        
        return;
    }

    private function createDateInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::DATE_TITLE);
        array_push($this->title_list, self::DATE_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createDateInfoElements();

        array_push($this->header_row_index_list, $this->current_row);
        $this->addBlankRow();

        return; 
    }

     private function createObjetInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::OBJET);
        array_push($this->title_list, self::OBJET);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createObjetInfoElements();

        array_push($this->header_row_index_list, $this->current_row); 

        $this->addBlankRow();

        return;
    }

    private function createExpediteurInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::EXPED);
        array_push($this->title_list, self::EXPED);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createExpediteurInfoElements();

        array_push($this->header_row_index_list, $this->current_row); 
        $this->addBlankRow();

        return;
    }

    private function createListeContactInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::LISTE);
        array_push($this->title_list, self::LISTE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createListInfoElements();

        array_push($this->header_row_index_list, $this->current_row); 
        $this->addBlankRow();

        return;
    }

     private function createModelEmailInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::EMAIL_TITLE);
        array_push($this->title_list, self::EMAIL_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createEmailInfoElements();

        array_push($this->header_row_index_list, $this->current_row); 
        $this->addBlankRow();

        return;
    }

    private function addBlankRow()
    {
        $this->current_row++;
        $this->current_col = 0;
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, "");
    }

    public function createResponse()
    {
        $writer = $this->create();
        $response = $this->php_excel->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            self::FILE_NAME_AND_EXT
        );

        $response->headers->set("Content-Type", "text/csv; charset=utf-8");
        $response->headers->set("Pragma", "public");
        $response->headers->set("Cache-Control", "maxage=1");
        $response->headers->set("Content-Disposition", $dispositionHeader);

        return $response;
    }

   

    public function getTitleList()
    {
        return $this->title_list;
    }

    public function getTitleRowIndexList()
    {
        return $this->title_row_index_list;
    }

    public function getHeaderRowIndexList()
    {
        return $this->header_row_index_list;
    }
    
}