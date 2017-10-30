<?php
namespace AdminBundle\Service\ImportExport;

use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

class RegistrationModel
{
    private $phpExcel;
    private $phpExcelObject;
    private $em;
    private $current_row;
    private $current_col;

    const WRITER_TYPE = "CSV";
//    const WRITER_TYPE = "Excel5";
    const FILE_NAME_AND_EXT = "modele.csv";
//    const FILE_NAME_AND_EXT = "modele.xls";
    const COMPANY_INFOS_TITLE = "Coordonnées de la société";
    const USER_INFOS_TITLE = "Coordonnées du bénéficiaire";

    public function __construct(PHPExcelFactory $factory, EntityManager $em)
    {
        $this->phpExcel = $factory;
        $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();
        $this->em = $em;

        $this->current_row = 1; // 1-based index
        $this->current_col = 0;
    }

    private function create()
    {
        $this->createCompanyInfoBlock();
        $this->createUserInfoBlock();
        $writer = $this->phpExcel->createWriter($this->phpExcelObject, self::WRITER_TYPE);

        return $writer;
    }

    private function createCompanyInfoBlock()
    {
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::COMPANY_INFOS_TITLE);
        $this->current_row++;

        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_NAME);
        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_POSTAL_ADDRESS);
        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_POSTAL_CODE);
        $this->createInfoElement(SpecialFieldIndex::USER_COMPANY_CITY);

        $this->phpExcelObject->setActiveSheetIndex(0)
            ->mergeCellsByColumnAndRow(0, $this->current_row - 1, $this->current_col - 1, $this->current_row - 1);
    }

    private function createUserInfoBlock()
    {
        $this->current_col = 0;
        $this->current_row += 2;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::USER_INFOS_TITLE);
        $this->current_row++;

        $this->createInfoElement(SpecialFieldIndex::USER_NAME);
        $this->createInfoElement(SpecialFieldIndex::USER_FIRSTNAME);
        $this->createInfoElement(SpecialFieldIndex::USER_EMAIL);
        $this->createInfoElement(SpecialFieldIndex::USER_CIVILITY);
        $this->createInfoElement(SpecialFieldIndex::USER_PRO_EMAIL);
        $this->createInfoElement(SpecialFieldIndex::USER_PHONE);
        $this->createInfoElement(SpecialFieldIndex::USER_MOBILE_PHONE);

        $this->addBlankRow();
        $this->addBlankRow();
        $this->addBlankRow();
    }

    private function createInfoElement($special_field_index)
    {
        $field = $this->em->getRepository(SiteFormFieldSetting::class)
            ->findOneBy(
                array(
                    'special_field_index' => $special_field_index,
                    'published' => true,
                )
            );
        if (!is_null($field)) {
            $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $field->getLabel());
            $this->current_col++;
        }
    }

    private function addBlankRow()
    {
        $this->current_row++;
        $this->current_col = 0;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, '');
    }

    public function createResponse()
    {
        $writer = $this->create();
        $response = $this->phpExcel->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            self::FILE_NAME_AND_EXT
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}