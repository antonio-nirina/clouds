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
    private $import_file;
    private $container;
    private $error_list;
    private $php_excel;
    private $model;
    private $filesystem;
    private $csv_handler;
    private $schema_checker;
    private $importer;
    private $site_form_setting;

    public function __construct(
        ContainerInterface $container,
        PHPExcelFactory $php_excel,
        RegistrationModel $model,
        Filesystem $filesystem,
        CSVHandler $csv_handler,
        RegistrationSchemaChecker $schema_checker,
        RegistrationImporter $importer
    ) {
        $this->container = $container;
        $this->php_excel = $php_excel;
        $this->model = $model;
        $this->filesystem = $filesystem;
        $this->error_list = array();
        $this->csv_handler = $csv_handler;
        $this->schema_checker = $schema_checker;
        $this->importer = $importer;
    }

    public function getErrorList()
    {
        return $this->error_list;
    }

    public function import(UploadedFile $file)
    {
        $this->uploadImportFile($file);
        $import_file_path = $this->container->getParameter('registration_import_file_upload_dir')
            . '/' . $file->getClientOriginalName();
        $array_import_file = $this->csv_handler->createArray($import_file_path);

        $this->model->save();
        $this->schema_checker->setSiteFormSetting($this->site_form_setting);
        $error_list = $this->schema_checker->check($this->model, $array_import_file);
        if (!empty($error_list)) {
            $this->error_list = $error_list;
            $this->removeFile($import_file_path);
            $this->model->removeSavedFile();
            return $this->error_list;
        } else {
            $this->importer->setSiteFormSetting($this->site_form_setting);
            $this->importer->importData($this->model, $array_import_file);
        }
        $this->removeFile($import_file_path);
        $this->model->removeSavedFile();

        return $this->error_list;
    }

    private function uploadImportFile(UploadedFile $file)
    {
        $file->move(
            $this->container->getParameter('registration_import_file_upload_dir'),
            $file->getClientOriginalName()
        );

        return;
    }

    private function removeFile($file_path)
    {
        if ($this->filesystem->exists($file_path)) {
            $this->filesystem->remove($file_path);
        }

        return;
    }

    public function setSiteFormSetting(SiteFormSetting $site_form_setting)
    {
        $this->site_form_setting = $site_form_setting;

        return;
    }
}