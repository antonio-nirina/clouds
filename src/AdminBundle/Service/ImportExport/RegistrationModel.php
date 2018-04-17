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

class RegistrationModel
{
    private $phpExcel;
    private $phpExcelObject;
    private $em;
    private $currentRow;
    private $currentCol;
    private $filesystem;
    private $siteFormSetting;

    const WRITER_TYPE = "CSV";
    const FILE_NAME_AND_EXT = "modele.csv";
    const COMPANY_INFOS_TITLE = "Coordonnées de la société";
    const USER_INFOS_TITLE = "Coordonnées du bénéficiaire";

    private $companyHeaderList;
    private $userHeaderList;

    private $container;

    private $savePath;

    private $titleList;
    private $titleRowIndexList;
    private $headerRowIndexList;

    public function __construct(
        PHPExcelFactory $factory,
        EntityManager $em,
        ContainerInterface $container,
        Filesystem $filesystem
    ) {
        $this->phpExcel = $factory;
        $this->phpExcelObject = $this->phpExcel->createPHPExcelObject();
        $this->em = $em;

        $this->currentRow = 1; // 1-based index
        $this->currentCol = 0;

        $this->companyHeaderList = array();
        $this->userHeaderList = array();

        $this->container = $container;
        $this->filesystem = $filesystem;

        $this->titleList = array();
        $this->titleRowIndexList = array();
        $this->headerRowIndexList = array();
    }

    /**
     * @param $siteFormSetting
     */
    public function setSiteFormSetting($siteFormSetting)
    {
        $this->siteFormSetting = $siteFormSetting;
    }

    /**
     * @return array
     */
    public function getCompanyHeaderList()
    {
        return $this->companyHeaderList;
    }

    /**
     * @return array
     */
    public function getUserHeaderList()
    {
        return $this->userHeaderList;
    }

    /**
     * @return \PHPExcel
     */
    private function createObject()
    {
        $this->createCompanyInfoBlock();
        $this->createUserInfoBlock();
        return $this->phpExcelObject;
    }

    /**
     * @return \PHPExcel_Writer_IWriter
     */
    private function create()
    {
        $this->createObject();
        $writer = $this->phpExcel->createWriter($this->phpExcelObject, self::WRITER_TYPE);
        $writer->setDelimiter(';');

        return $writer;
    }

    /**
     * @param $specialFieldIndex
     * @param $headerList
     */
    private function createInfoElements($specialFieldIndex, $headerList)
    {
        if (is_null($this->siteFormSetting)) {
            throw new NoSiteFormSettingSetException("No site form setting set!");
        }

        $userCompanyFieldList = $this->em->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
            ->findListBySiteFormSettingAndSpecialIndex($this->siteFormSetting, $specialFieldIndex);

        if (!empty($userCompanyFieldList)) {
            foreach ($userCompanyFieldList as $field) {
                $this->phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, $field->getLabel());
                $this->currentCol++;
                array_push($headerList, $field->getLabel());
            }
        }

        return;
    }

    /**
     * create Company
     */
    private function createCompanyInfoElements()
    {
        $this->createInfoElements(SpecialFieldIndex::USER_COMPANY_FIELD, $this->companyHeaderList);

        return;
    }

    /**
     * create user element
     */
    private function createUserInfoElements()
    {
        $this->createInfoElements(SpecialFieldIndex::USER_FIELD, $this->userHeaderList);

        return;
    }

    /**
     * create info block
     */
    private function createCompanyInfoBlock()
    {
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, self::COMPANY_INFOS_TITLE);
        array_push($this->titleList, self::COMPANY_INFOS_TITLE);
        array_push($this->titleRowIndexList, $this->currentRow);
        $this->currentRow++;

        $this->createCompanyInfoElements();

        array_push($this->headerRowIndexList, $this->currentRow); // to save company data headers row index

        /* $this->phpExcelObject->setActiveSheetIndex(0)
            ->mergeCellsByColumnAndRow(0, $this->currentRow - 1, $this->currentCol - 1, $this->currentRow - 1);*/
        $this->addBlankRow();

        return;
    }

    /**
     * create user block
     */
    private function createUserInfoBlock()
    {
        $this->currentCol = 0;
        $this->currentRow += 2;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, self::USER_INFOS_TITLE);
        array_push($this->titleList, self::USER_INFOS_TITLE);
        array_push($this->titleRowIndexList, $this->currentRow);
        $this->currentRow++;

        $this->createUserInfoElements();

        array_push($this->headerRowIndexList, $this->currentRow); // to save user data headers row index

        $this->addBlankRow();
    }

    /**
     * add blank
     */
    private function addBlankRow()
    {
        $this->currentRow++;
        $this->currentCol = 0;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, "");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function createResponse()
    {
        $writer = $this->create();
        $response = $this->phpExcel->createStreamedResponse($writer);
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

    /**
     * save
     */
    public function save()
    {
        $writer = $this->create();
        $savePath = $this->container->getParameter("registration_model_dir") . '/' . self::FILE_NAME_AND_EXT;
        $this->savePath = $savePath;
        $writer->save($savePath);

        return;
    }

    /**
     * delete save file
     */
    public function removeSavedFile()
    {
        $filePath = $this->container->getParameter("registration_model_dir") . '/' . self::FILE_NAME_AND_EXT;
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }

        return;
    }

    /**
     * @return mixed
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     * @return array
     */
    public function getTitleList()
    {
        return $this->titleList;
    }

    /**
     * @return array
     */
    public function getTitleRowIndexList()
    {
        return $this->titleRowIndexList;
    }

    /**
     * @return array
     */
    public function getHeaderRowIndexList()
    {
        return $this->headerRowIndexList;
    }
}
