<?php

namespace AdminBundle\Service\ImportExport;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use AdminBundle\Service\ImportExport\RegistrationModel;
use Symfony\Component\Filesystem\Filesystem;
use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\ImportExport\RegistrationSchemaChecker;
use AdminBundle\Entity\SiteFormSetting;

class RegistrationHandler
{
    private $importFile;
    private $container;
    private $errorList;
    private $phpExcel;
    private $model;
    private $filesystem;
    private $csvHandler;
    private $schemaChecker;
    private $importer;
    private $siteFormSetting;

    /**
     * RegistrationHandler constructor.
     * @param ContainerInterface $container
     * @param PHPExcelFactory $phpExcel
     * @param \AdminBundle\Service\ImportExport\RegistrationModel $model
     * @param Filesystem $filesystem
     * @param CSVHandler $csvHandler
     * @param \AdminBundle\Service\ImportExport\RegistrationSchemaChecker $schemaChecker
     * @param RegistrationImporter $importer
     */
    public function __construct(
        ContainerInterface $container,
        PHPExcelFactory $phpExcel,
        RegistrationModel $model,
        Filesystem $filesystem,
        CSVHandler $csvHandler,
        RegistrationSchemaChecker $schemaChecker,
        RegistrationImporter $importer
    ) {
        $this->container = $container;
        $this->phpExcel = $phpExcel;
        $this->model = $model;
        $this->filesystem = $filesystem;
        $this->errorList = array();
        $this->csvHandler = $csvHandler;
        $this->schemaChecker = $schemaChecker;
        $this->importer = $importer;
    }

    /**
     * @return array
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    public function import(UploadedFile $file)
    {
        $this->uploadImportFile($file);
        $importFilePath = $this->container->getParameter('registration_import_file_upload_dir')
            . '/' . $file->getClientOriginalName();
        $arrayImportFile = $this->csvHandler->createArray($importFilePath);

        $this->model->setSiteFormSetting($this->siteFormSetting);
        $this->model->save();
        $this->schemaChecker->setSiteFormSetting($this->siteFormSetting);
        $errorList = $this->schemaChecker->check($this->model, $arrayImportFile);

        if (!empty($errorList)) {
            $this->errorList = $errorList;
            $this->removeFile($importFilePath);
            $this->model->removeSavedFile();
            return $this->errorList;
        } else {
            $this->importer->setSiteFormSetting($this->siteFormSetting);
            $this->importer->importData($this->model, $arrayImportFile);
        }
        $this->removeFile($importFilePath);
        $this->model->removeSavedFile();

        return $this->errorList;
    }

    /**
     * @param UploadedFile $file
     */
    private function uploadImportFile(UploadedFile $file)
    {
        $file->move(
            $this->container->getParameter('registration_import_file_upload_dir'),
            $file->getClientOriginalName()
        );

        return;
    }

    /**
     * @param $filePath
     */
    private function removeFile($filePath)
    {
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }

        return;
    }

    /**
     * @param SiteFormSetting $siteFormSetting
     */
    public function setSiteFormSetting(SiteFormSetting $siteFormSetting)
    {
        $this->siteFormSetting = $siteFormSetting;

        return;
    }
}
