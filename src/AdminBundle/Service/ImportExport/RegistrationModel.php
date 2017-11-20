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
    private $php_excel;
    private $php_excel_object;
    private $em;
    private $current_row;
    private $current_col;
    private $filesystem;
    private $site_form_setting;

    const WRITER_TYPE = "CSV";
    const FILE_NAME_AND_EXT = "modele.csv";
    const COMPANY_INFOS_TITLE = "Coordonnées de la société";
    const USER_INFOS_TITLE = "Coordonnées du bénéficiaire";

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

    public function setSiteFormSetting($site_form_setting)
    {
        $this->site_form_setting = $site_form_setting;
    }

    public function getCompanyHeaderList()
    {
        return $this->company_header_list;
    }

    public function getUserHeaderList()
    {
        return $this->user_header_list;
    }

    private function createObject()
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

    private function createInfoElements($special_field_index, $header_list)
    {
        if (is_null($this->site_form_setting)) {
            throw new NoSiteFormSettingSetException("No site form setting set!");
        }

        $user_company_field_list = $this->em->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
            ->findListBySiteFormSettingAndSpecialIndex($this->site_form_setting, $special_field_index);

        if (!empty($user_company_field_list)) {
            foreach ($user_company_field_list as $field) {
                $this->php_excel_object->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $field->getLabel());
                $this->current_col++;
                array_push($header_list, $field->getLabel());
            }
        }

        return;
    }

    private function createCompanyInfoElements()
    {
        $this->createInfoElements(SpecialFieldIndex::USER_COMPANY_FIELD, $this->company_header_list);

        return;
    }

    private function createUserInfoElements()
    {
        $this->createInfoElements(SpecialFieldIndex::USER_FIELD, $this->user_header_list);

        return;
    }

    private function createCompanyInfoBlock()
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::COMPANY_INFOS_TITLE);
        array_push($this->title_list, self::COMPANY_INFOS_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createCompanyInfoElements();

        array_push($this->header_row_index_list, $this->current_row); // to save company data headers row index

       /* $this->php_excel_object->setActiveSheetIndex(0)
            ->mergeCellsByColumnAndRow(0, $this->current_row - 1, $this->current_col - 1, $this->current_row - 1);*/
        $this->addBlankRow();

        return;
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

        $this->createUserInfoElements();

        array_push($this->header_row_index_list, $this->current_row); // to save user data headers row index

        $this->addBlankRow();
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

        return;
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