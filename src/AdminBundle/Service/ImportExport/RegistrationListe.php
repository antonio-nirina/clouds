<?php

namespace AdminBundle\Service\ImportExport;

use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use AdminBundle\Exception\NoSiteFormSettingSetException;
use AdminBundle\Service\Statistique\Common;

class RegistrationListe
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
    const EMAIL_TITLE = "Email";
    const STATUS = "Status";

    private $date;
    private $email;
    private $status;

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
        $this->status = [];

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
        $this->createStatusInfoBlock();
        $this->createEmailInfoBlock();
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
        $field = $this->common->getOneCampagne($this->site_form_setting);
         if (!empty($field)) {
            $res = $field["status"]["LastActivityAt"];
            $date = new \DateTime($res);
            $var = $date->format("d/m/Y");
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $var);
                $this->current_row++;
                array_push($this->date, $var);
            
        }

        return;
    }

    private function createStatusInfoElements()
    {
        $field = $this->common->getOneCampagne($this->site_form_setting);

        if (!empty($field)) {
            foreach ($field["email"] as $value) {
                if ($value["etat"] == "sent") {
                    $val = "Délivrés";
                } elseif ($value["etat"] == "opened") {
                    $val = "Ouverts";
                } elseif ($value["etat"] == "clicked") {
                    $val = "Cliqués";
                }  elseif ($value["etat"] == "bounce") {
                    $val ="Erreur";
                } elseif ($value["etat"] == "spam") {
                    $val = "Spam";
                } elseif ($value["etat"] == "unsub") {
                    $val = "Désabonnés";
                } elseif ($value["etat"] == "blocked") {
                    $val = "Bloqués";
                }
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $val);
                $this->current_row++;
                array_push($this->status, $value["etat"]);
            }
            
        }

        return;
    }

    private function createEmailInfoElements()
    {
        $field = $this->common->getOneCampagne($this->site_form_setting);
         if (!empty($field)) {
            foreach ($field["email"] as $value) {
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $value["emails"]);
                $this->current_row++;
                array_push($this->email, $value["emails"]);
            }
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

        array_push($this->header_row_index_list, $this->current_row); // to save company data headers row index

       /* $this->php_excel_object->setActiveSheetIndex(0)
            ->mergeCellsByColumnAndRow(0, $this->current_row - 1, $this->current_col - 1, $this->current_row - 1);*/
        $this->addBlankRow();

        return;
    }

    private function createStatusInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::STATUS);
        array_push($this->title_list, self::STATUS);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createStatusInfoElements();

        array_push($this->header_row_index_list, $this->current_row); // to save company data headers row index

       /* $this->php_excel_object->setActiveSheetIndex(0)
            ->mergeCellsByColumnAndRow(0, $this->current_row - 1, $this->current_col - 1, $this->current_row - 1);*/
        $this->addBlankRow();

        return;
    }

    private function createEmailInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::EMAIL_TITLE);
        array_push($this->title_list, self::EMAIL_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createEmailInfoElements();

        array_push($this->header_row_index_list, $this->current_row); // to save company data headers row index

       /* $this->php_excel_object->setActiveSheetIndex(0)
            ->mergeCellsByColumnAndRow(0, $this->current_row - 1, $this->current_col - 1, $this->current_row - 1);*/
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
