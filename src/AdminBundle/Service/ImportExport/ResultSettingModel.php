<?php
namespace AdminBundle\Service\ImportExport;

use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ResultSettingModel
{
    private $php_excel;
    private $php_excel_object;
    private $em;
    private $current_row;
    private $current_col;
    private $filesystem;

    const WRITER_TYPE = "CSV";
    const FILE_NAME_AND_EXT = "modele.csv";
    const USER_INFOS_TITLE = "Coordonnées du bénéficiaire";
    const PRODUCT_INFOS_TITLE = "Chiffre d'affaires";
    const RANK_INFOS_TITLE = "Fonction commerciale";

    const USER_SPECIAL_FIELD_INDEX_LIST = array(
        'Nom',
        'Prénom'
        /*SpecialFieldIndex::USER_EMAIL,
        SpecialFieldIndex::USER_CIVILITY,
        SpecialFieldIndex::USER_PRO_EMAIL,
        SpecialFieldIndex::USER_PHONE,
        SpecialFieldIndex::USER_MOBILE_PHONE*/
    );

    const RANK_SPECIAL_FIELD_INDEX_LIST = array(
        "Fonction",
        "Rang",
        // "Réseau"
    );

    private $user_header_list;
    private $rank_header_list;
    private $product_header_list;

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
        
        $this->user_header_list = array();
        $this->rank_header_list = array();
        $this->product_header_list = array();

        $this->container = $container;
        $this->filesystem = $filesystem;

        $this->title_list = array();
        $this->title_row_index_list = array();
        $this->header_row_index_list = array();
    }

    public function getUserHeaderList()
    {
        return $this->user_header_list;
    }

    public function createObject($monthly = false, $by_product = false, $by_rank = false)
    {
        $this->createUserInfoBlock();
        if ($by_rank) {
            $this->createRankInfoBlock();
        }
        if ($by_product) {
            $this->createProductInfoBlock(5);
        } else {
            $this->createProductInfoBlock();
        }
        $this->createPeriodInfoBlock($monthly);

        return $this->php_excel_object;
    }

    private function create($monthly = false, $by_product = false, $by_rank = false)
    {
        $this->createObject($monthly, $by_product, $by_rank);
        $writer = $this->php_excel->createWriter($this->php_excel_object, self::WRITER_TYPE);

        return $writer;
    }

    private function createUserInfoBlock()
    {
        // $this->current_col = 0;
        // $this->current_row += 2;
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::USER_INFOS_TITLE);
        array_push($this->title_list, self::USER_INFOS_TITLE);
        array_push($this->title_row_index_list, $this->current_row);
        $this->current_row++;

        $this->createInfoElement("Nom");
        $this->createInfoElement("Prénom");
        // $this->createInfoElement(SpecialFieldIndex::USER_EMAIL);
        // $this->createInfoElement(SpecialFieldIndex::USER_CIVILITY);
        // $this->createInfoElement(SpecialFieldIndex::USER_PRO_EMAIL);
        // $this->createInfoElement(SpecialFieldIndex::USER_PHONE);
        // $this->createInfoElement(SpecialFieldIndex::USER_MOBILE_PHONE);
        // $this->createInfoElement(SpecialFieldIndex::USER_PASSWORD);
        array_push($this->header_row_index_list, $this->current_row); // to save user data headers row index

        // $this->addBlankRow();
    }

    private function createRankInfoBlock()
    {
        $this->current_row -= 1;
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::RANK_INFOS_TITLE);
        array_push($this->title_list, self::RANK_INFOS_TITLE);

        if (!in_array($this->current_row, $this->title_row_index_list)) {
            array_push($this->title_row_index_list, $this->current_row);
        }
        $this->current_row++;

        $this->createSimpleInfoElement("Fonction");
        $this->createSimpleInfoElement("Rang");
        // $this->createSimpleInfoElement("Réseau");

        if (!in_array($this->current_row, $this->header_row_index_list)) {
            array_push($this->header_row_index_list, $this->current_row); // to save user data headers row index
        }
        // $this->addBlankRow();
    }

    private function createProductInfoBlock($nb = 1)
    {
        $this->current_row -= 1;
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, self::PRODUCT_INFOS_TITLE);
        array_push($this->title_list, self::PRODUCT_INFOS_TITLE);

        if (!in_array($this->current_row, $this->title_row_index_list)) {
            array_push($this->title_row_index_list, $this->current_row);
        }
        $this->current_row++;

        for ($i = 1; $i <= $nb; $i++) {
            $this->createSimpleInfoElement("Produit $i");
            // $this->createSimpleInfoElement("Précédent $i");

            if ($nb > 1) {
                $this->createSimpleInfoElement("Dénomination $i");
            }
        }

        if (!in_array($this->current_row, $this->header_row_index_list)) {
            array_push($this->header_row_index_list, $this->current_row); // to save user data headers row index
        }
        // $this->addBlankRow();
    }

    private function createPeriodInfoBlock($monthly = false)
    {
        if (!$monthly) {
            $this->createSimpleInfoElement("Date");
        } else {
            $this->createSimpleInfoElement("Période de");
            $this->createSimpleInfoElement("à");
        }
    }

    private function createSimpleInfoElement($header)
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $header);
        $this->current_col++;
        
        if (in_array($header, self::RANK_SPECIAL_FIELD_INDEX_LIST)) {
            array_push($this->rank_header_list, $header);
        } else {
            array_push($this->product_header_list, $header);
        }
    }

    private function createInfoElement($special_field_index)
    {
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, $special_field_index);
        $this->current_col++;

        if (in_array($special_field_index, self::USER_SPECIAL_FIELD_INDEX_LIST)) {
            array_push($this->user_header_list, $special_field_index);
        }
    }

    private function addBlankRow()
    {
        $this->current_row++;
        $this->current_col = 0;
        $this->php_excel_object->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->current_col, $this->current_row, "");
    }

    public function createResponse($monthly = false, $by_product = false, $by_rank = false)
    {
        $writer = $this->create($monthly, $by_product, $by_rank);
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

    public function save($monthly = false, $by_product = false, $by_rank = false)
    {
        $writer = $this->create($monthly, $by_product, $by_rank);
        $save_path = $this->container->getParameter("result_setting_model").'/'.self::FILE_NAME_AND_EXT;
        $this->save_path = $save_path;
        $writer->save($save_path);
    }

    public function removeSavedFile()
    {
        $file_path = $this->container->getParameter("result_setting_model").'/'.self::FILE_NAME_AND_EXT;
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
