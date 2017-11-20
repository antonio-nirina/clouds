<?php

namespace AdminBundle\Service\ImportExport;

use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class RegistrationModel
{
    private $php_excel;
    private $php_excel_object;
    private $em;
    private $current_row;
    private $current_col;
    private $filesystem;

    const WRITER_TYPE = "CSV";
    const FILE_NAME_AND_EXT = "modele.csv";
    const COMPANY_INFOS_TITLE = "Coordonnées de la société";
    const USER_INFOS_TITLE = "Coordonnées du bénéficiaire";

    const COMPANY_SPECIAL_FIELD_INDEX_LIST = array(
        SpecialFieldIndex::USER_COMPANY_NAME,
        SpecialFieldIndex::USER_COMPANY_POSTAL_ADDRESS,
        SpecialFieldIndex::USER_COMPANY_POSTAL_CODE,
        SpecialFieldIndex::USER_COMPANY_CITY
    );

    const USER_SPECIAL_FIELD_INDEX_LIST = array(
        SpecialFieldIndex::USER_NAME,
        SpecialFieldIndex::USER_FIRSTNAME,
        SpecialFieldIndex::USER_EMAIL,
        SpecialFieldIndex::USER_CIVILITY,
        SpecialFieldIndex::USER_PRO_EMAIL,
        SpecialFieldIndex::USER_PHONE,
        SpecialFieldIndex::USER_MOBILE_PHONE
    );

    private $company_header_list;
    private $user_header_list;

    private $container;

    private $save_path;

    private $title_list;
    private $title_row_index_list;
    private $header_row_index_list;

    public function __construct(
        PHPExcelFactory $factory,
        EntityManager $em,
        ContainerInterface $container,
        Filesystem $filesystem
    ) {
        $this->php_excel = $factory;
        $this->php_excel_object = $this->php_excel->createPHPExcelObject();
        $this->em = $em;

        $this->current_row = 1; // 1-based index
        $this->current_col = 0;

        $this->company_header_list = array();
        $this->user_header_list = array();

        $this->container = $container;
        $this->filesystem = $filesystem;

        $this->title_list = array();
        $this->title_row_index_list = array();
        $this->header_row_index_list = array();
    }

    public function getCompanyHeaderList()
    {
        return $this->company_header_list;
    }

    public function getUserHeaderList()
    {
        return $this->user_header_list;
    }

    public function createObject()
    {
        $this->createCompanyInfoBlock();
        $this->createUserInfoBlock();
        return $this->php_excel_object;
    }

    private function create()
    {
        $this->createObject();
        $writer = $this->php_excel->createWriter($this->php_excel_object, self::WRITER_TYPE);

        return $writer;
    }

    private function createCompanyInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::COMPANY_INFOS_TITLE);
        array_push($this->title_list, self::COMPANY_INFOS_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_NAME);
        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_POSTAL_ADDRESS);
        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_POSTAL_CODE);
        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_CITY);
        array_push($this->header_row_index_list, $this->current_row); // to save company data headers row index

//        $this->php_excel_object->setActiveSheetIndex(0)
//            ->mergeCellsByColumnAndRow(0, $this->current_row - 1, $this->current_col - 1, $this->current_row - 1);
    }

    private function createUserInfoBlock()
    {
        $this->current_col = 0;
        $this->current_row += 2;
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::USER_INFOS_TITLE);
        array_push($this->title_list, self::USER_INFOS_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createInfoElement(SpecialFieldIndex::USER_NAME);
        $this->createInfoElement(SpecialFieldIndex::USER_FIRSTNAME);
        $this->createInfoElement(SpecialFieldIndex::USER_EMAIL);
        $this->createInfoElement(SpecialFieldIndex::USER_CIVILITY);
        $this->createInfoElement(SpecialFieldIndex::USER_PRO_EMAIL);
        $this->createInfoElement(SpecialFieldIndex::USER_PHONE);
        $this->createInfoElement(SpecialFieldIndex::USER_MOBILE_PHONE);
        $this->createInfoElement(SpecialFieldIndex::USER_PASSWORD);
        array_push($this->header_row_index_list, $this->current_row); // to save user data headers row index

        $this->addBlankRow();
    }

    private function createInfoElement($special_field_index)
    {
        // /!\ NEED TO ADD "SITE FORM" PARAMETER, to distinguish which form is used, which form is connected to fields
        $field = $this->em->getRepository(SiteFormFieldSetting::class)
            ->findOneBy(
                array(
                    "special_field_index" => $special_field_index,
                    "published" => true,
                )
            );
        if (!is_null($field)) {
            $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $field->getLabel());
            $this->current_col++;

            if (in_array($special_field_index, self::COMPANY_SPECIAL_FIELD_INDEX_LIST)) {
                array_push($this->company_header_list, $field->getLabel());
            } elseif (in_array($special_field_index, self::USER_SPECIAL_FIELD_INDEX_LIST)) {
                array_push($this->user_header_list, $field->getLabel());
            }
        }
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

    public function save()
    {
        $writer = $this->create();
        $save_path = $this->container->getParameter("registration_model_dir").'/'.self::FILE_NAME_AND_EXT;
        $this->save_path = $save_path;
        $writer->save($save_path);
    }

    public function removeSavedFile()
    {
        $file_path = $this->container->getParameter("registration_model_dir").'/'.self::FILE_NAME_AND_EXT;
        if ($this->filesystem->exists($file_path)) {
            $this->filesystem->remove($file_path);
        }

        return;
    }

    public function getSavePath()
    {
        return $this->save_path;
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
