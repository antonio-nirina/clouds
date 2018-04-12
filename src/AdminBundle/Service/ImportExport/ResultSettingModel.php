<?php
namespace AdminBundle\Service\ImportExport;

use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use AdminBundle\Entity\Program;
use AdminBundle\Exception\NoRelatedProgramException;

class ResultSettingModel
{
    private $phpExcel;
    private $phpExcelObject;
    private $em;
    private $currentRow;
    private $currentCol;
    private $filesystem;
    private $program;

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

    private $userHeaderList;
    private $rankHeaderList;
    private $productHeaderList;

    private $container;
    private $savePath;

    private $titleList;
    private $titleRowIndexList;
    private $headerRowIndexList;

    /**
     * Set program
     *
     * @param Program $program
     */
    public function setProgram(Program $program)
    {
        $this->program = $program;
    }

    /**
     * ResultSettingModel constructor.
     * @param PHPExcelFactory $factory
     * @param EntityManager $em
     * @param ContainerInterface $container
     * @param Filesystem $filesystem
     */
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

        $this->userHeaderList = array();
        $this->rankHeaderList = array();
        $this->productHeaderList = array();

        $this->container = $container;
        $this->filesystem = $filesystem;

        $this->title_list = array();
        $this->titleRowIndexList = array();
        $this->headerRowIndexList = array();
    }

    /**
     * @return array
     */
    public function getUserHeaderList()
    {
        return $this->userHeaderList;
    }

    /**
     * @param bool $monthly
     * @param bool $byProduct
     * @param bool $byRank
     * @return \PHPExcel
     */
    public function createObject($monthly = false, $byProduct = false, $byRank = false)
    {
        $this->createUserInfoBlock();
        if ($byRank) {
            $this->createRankInfoBlock();
        }
        if ($byProduct) {
            $this->createProductInfoBlock(5);
        } else {
            $this->createProductInfoBlock();
        }
        $this->createPeriodInfoBlock($monthly);

        /**
         * Adding user data
         */
        $this->currentRow++;
        $this->createUserDataBlock();


        return $this->phpExcelObject;
    }

    /**
     * Creating user data block
     * for users linked to given program
     */
    public function createUserDataBlock()
    {
        if (is_null($this->program)) {
            throw new NoRelatedProgramException();
        }

        // getting special use case user (program user)
        $programUser = $this->em->getRepository('AdminBundle\Entity\ProgramUser')->findOneBy(array(
            'program' => $this->program,
            'specialUseCaseState' => true,
        ));

        if (!is_null($programUser)) {
            // getting corresponding app user
            /*$appUser = $this->em->getRepository('UserBundle\Entity\User')->findOneBy(array(
                'id' => $programUser->getAppUser($programUser)
            ));*/
            $appUser = $programUser->getAppUser();
            if (!is_null($appUser)) {
                $this->currentCol = 0;
                $this->createInfoElement($programUser->getId()); // ProgramUser ID but not AppUser ID
                $this->createInfoElement($appUser->getName());
                $this->createInfoElement($appUser->getFirstname());
            }
        }
    }

    /**
     * @param bool $monthly
     * @param bool $byProduct
     * @param bool $byRank
     * @return \PHPExcel_Writer_IWriter
     */
    private function create($monthly = false, $byProduct = false, $byRank = false)
    {
        $this->createObject($monthly, $byProduct, $byRank);
        $writer = $this->phpExcel->createWriter($this->phpExcelObject, self::WRITER_TYPE);

        return $writer;
    }

    /**
     *
     */
    private function createUserInfoBlock()
    {
        // $this->currentCol = 0;
        // $this->currentRow += 2;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, self::USER_INFOS_TITLE);
        array_push($this->titleList, self::USER_INFOS_TITLE);
        array_push($this->titleRowIndexList, $this->currentRow);
        $this->currentRow++;

        $this->createInfoElement('ID');
        $this->createInfoElement("Nom");
        $this->createInfoElement("Prénom");
        // $this->createInfoElement(SpecialFieldIndex::USER_EMAIL);
        // $this->createInfoElement(SpecialFieldIndex::USER_CIVILITY);
        // $this->createInfoElement(SpecialFieldIndex::USER_PRO_EMAIL);
        // $this->createInfoElement(SpecialFieldIndex::USER_PHONE);
        // $this->createInfoElement(SpecialFieldIndex::USER_MOBILE_PHONE);
        // $this->createInfoElement(SpecialFieldIndex::USER_PASSWORD);
        array_push($this->headerRowIndexList, $this->currentRow); // to save user data headers row index

        // $this->addBlankRow();
    }

    /**
     * create rank
     */
    private function createRankInfoBlock()
    {
        $this->currentRow -= 1;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, self::RANK_INFOS_TITLE);
        array_push($this->titleList, self::RANK_INFOS_TITLE);

        if (!in_array($this->currentRow, $this->titleRowIndexList)) {
            array_push($this->titleRowIndexList, $this->currentRow);
        }
        $this->currentRow++;

        $this->createSimpleInfoElement("Fonction");
        $this->createSimpleInfoElement("Rang");
        // $this->createSimpleInfoElement("Réseau");

        if (!in_array($this->currentRow, $this->headerRowIndexList)) {
            array_push($this->headerRowIndexList, $this->currentRow); // to save user data headers row index
        }
        // $this->addBlankRow();
    }

    /**
     * @param int $nb
     */
    private function createProductInfoBlock($nb = 1)
    {
        $this->currentRow -= 1;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, self::PRODUCT_INFOS_TITLE);
        array_push($this->titleList, self::PRODUCT_INFOS_TITLE);

        if (!in_array($this->currentRow, $this->titleRowIndexList)) {
            array_push($this->titleRowIndexList, $this->currentRow);
        }
        $this->currentRow++;

        for ($i = 1; $i <= $nb; $i++) {
            $this->createSimpleInfoElement("Produit $i");
            // $this->createSimpleInfoElement("Précédent $i");

            if ($nb > 1) {
                $this->createSimpleInfoElement("Dénomination $i");
            }
        }

        if (!in_array($this->currentRow, $this->headerRowIndexList)) {
            array_push($this->headerRowIndexList, $this->currentRow); // to save user data headers row index
        }
        // $this->addBlankRow();
    }

    /**
     * @param bool $monthly
     */
    private function createPeriodInfoBlock($monthly = false)
    {
        if (!$monthly) {
            $this->createSimpleInfoElement("Date");
        } else {
            $this->createSimpleInfoElement("Période de");
            $this->createSimpleInfoElement("à");
        }
    }

    /**
     * @param $header
     */
    private function createSimpleInfoElement($header)
    {
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, $header);
        $this->currentCol++;

        if (in_array($header, self::RANK_SPECIAL_FIELD_INDEX_LIST)) {
            array_push($this->rankHeaderList, $header);
        } else {
            array_push($this->productHeaderList, $header);
        }
    }

    /**
     * @param $specialFieldIndex
     */
    private function createInfoElement($specialFieldIndex)
    {
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, $specialFieldIndex);
        $this->currentCol++;

        if (in_array($specialFieldIndex, self::USER_SPECIAL_FIELD_INDEX_LIST)) {
            array_push($this->userHeaderList, $specialFieldIndex);
        }
    }

    /**
     *add blank
     */
    private function addBlankRow()
    {
        $this->currentRow++;
        $this->currentCol = 0;
        $this->phpExcelObject->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($this->currentCol, $this->currentRow, "");
    }

    /**
     * @param bool $monthly
     * @param bool $byProduct
     * @param bool $byRank
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function createResponse($monthly = false, $byProduct = false, $byRank = false)
    {
        $writer = $this->create($monthly, $byProduct, $byRank);
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
     * @param bool $monthly
     * @param bool $byProduct
     * @param bool $byRank
     */
    public function save($monthly = false, $byProduct = false, $byRank = false)
    {
        $writer = $this->create($monthly, $byProduct, $byRank);
        $savePath = $this->container->getParameter("result_setting_model") . '/' . self::FILE_NAME_AND_EXT;
        $this->savePath = $savePath;
        $writer->save($savePath);
    }

    /**
     * remove file
     */
    public function removeSavedFile()
    {
        $filePath = $this->container->getParameter("result_setting_model") . '/' . self::FILE_NAME_AND_EXT;
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
     * @return mixed
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
