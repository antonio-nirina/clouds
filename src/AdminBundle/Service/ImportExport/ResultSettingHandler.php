<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Entity\ResultSetting;
use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\ImportExport\ResultSettingImporter;
use AdminBundle\Service\ImportExport\ResultSettingModel;
use AdminBundle\Service\ImportExport\ResultSettingSchemaChecker;
use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AdminBundle\Entity\Program;

class ResultSettingHandler
{
    private $import_file;
    private $container;
    private $error_list;
    private $php_excel;
    private $model;
    private $filesystem;
    private $csv_handler;
    private $schema_checker;
    private $program;

    public function __construct(
        ContainerInterface $container,
        PHPExcelFactory $php_excel,
        ResultSettingModel $model,
        Filesystem $filesystem,
        CSVHandler $csv_handler,
        ResultSettingSchemaChecker $schema_checker
    ) {
        $this->container = $container;
        $this->php_excel = $php_excel;
        $this->model = $model;
        $this->filesystem = $filesystem;
        $this->error_list = array();
        $this->csv_handler = $csv_handler;
        $this->schema_checker = $schema_checker;
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
        return $this->error_list;
    }

    public function import(UploadedFile $file)
    {
        $this->uploadImportFile($file);
        $import_file_path = $this->container->getParameter('result_setting_upload')
            . '/' . $file->getClientOriginalName();
        $data_import_file = $this->csv_handler->createArray($import_file_path);

        $monthly = $this->result_setting->getMonthly();
        $by_product = $this->result_setting->getByProduct();
        $by_rank = $this->result_setting->getByRank();

        $this->model->setProgram($this->program);
        $this->model->save($monthly, $by_product, $by_rank);

        $error_list = $this->schema_checker->check($this->model, $data_import_file);

        if (!empty($error_list)) {
            $this->error_list = $error_list;
            $this->removeFile($import_file_path);
            $this->model->removeSavedFile();
            return $this->error_list;
        } else {
            $error_list = $this->schema_checker->import($this->model, $data_import_file);
            if (empty($error_list)) {//attribution des points perform
                $sales_point_attribution = $this->container
                    ->get('AdminBundle\Service\PointAttribution\SalesPointAttribution');
                $program = $this->container->get('admin.program')->getCurrent();
                $sales_point_attribution->closeClassmentProgression($program, $monthly);
            }
        }

        $this->removeFile($import_file_path);
        $this->model->removeSavedFile();
        return $this->error_list;
    }

    private function uploadImportFile(UploadedFile $file)//copy file server side
    {
        $file->move(
            $this->container->getParameter('result_setting_upload'),
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

    public function setResultSetting(ResultSetting $result_setting)
    {
        $this->result_setting = $result_setting;
    }
}
