<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Entity\ResultSetting;
use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\ImportExport\ResultSettingModel;
use AdminBundle\Service\ImportExport\ResultSettingSchemaChecker;
use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AdminBundle\Entity\Program;

class ResultSettingHandler
{
    private $importFile;
    private $container;
    private $errorList;
    private $phpExcel;
    private $model;
    private $filesystem;
    private $csvHandler;
    private $schemaChecker;
    private $program;

    public function __construct(
        ContainerInterface $container,
        PHPExcelFactory $phpExcel,
        ResultSettingModel $model,
        Filesystem $filesystem,
        CSVHandler $csvHandler,
        ResultSettingSchemaChecker $schemaChecker
    ) {
        $this->container = $container;
        $this->phpExcel = $phpExcel;
        $this->model = $model;
        $this->filesystem = $filesystem;
        $this->errorList = array();
        $this->csvHandler = $csvHandler;
        $this->schemaChecker = $schemaChecker;
    }

    /**
     * Set program
     *
     * @param Program $program
     */
    public function setProgram(Program $program)
    {
        $this->program = $program;
    }

    public function getErrorList()
    {
        return $this->errorList;
    }

    public function import(UploadedFile $file)
    {
        $this->uploadImportFile($file);
        $importFilePath = $this->container->getParameter('result_setting_upload')
            . '/' . $file->getClientOriginalName();
        $dataImportFile = $this->csvHandler->createArray($importFilePath);

        $monthly = $this->resultSetting->getMonthly();
        $byProduct = $this->resultSetting->getByProduct();
        $byRank = $this->resultSetting->getByRank();

        $this->model->setProgram($this->program);
        $this->model->save($monthly, $byProduct, $byRank);

        $errorList = $this->schemaChecker->check($this->model, $dataImportFile);

        if (!empty($errorList)) {
            $this->errorList = $errorList;
            $this->removeFile($importFilePath);
            $this->model->removeSavedFile();
            return $this->errorList;
        } else {
            $errorList = $this->schemaChecker->import($this->model, $dataImportFile);
            if (empty($errorList)) {//attribution des points perform
                $salesPointAttribution = $this->container
                    ->get('AdminBundle\Service\PointAttribution\SalesPointAttribution');
                $program = $this->container->get('admin.program')->getCurrent();
                $salesPointAttribution->closeClassmentProgression($program, $monthly);
            }
        }

        $this->removeFile($importFilePath);
        $this->model->removeSavedFile();
        return $this->errorList;
    }

    private function uploadImportFile(UploadedFile $file)//copy file server side
    {
        $file->move(
            $this->container->getParameter('result_setting_upload'),
            $file->getClientOriginalName()
        );

        return;
    }

    private function removeFile($filePath)
    {
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }
        return;
    }

    public function setResultSetting(ResultSetting $resultSetting)
    {
        $this->resultSetting = $resultSetting;
    }
}
