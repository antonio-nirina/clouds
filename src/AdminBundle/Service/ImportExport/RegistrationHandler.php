<?php
namespace AdminBundle\Service\ImportExport;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Liuggio\ExcelBundle\Factory as PHPExcelFactory;
use AdminBundle\Service\ImportExport\RegistrationModel;
use Symfony\Component\Filesystem\Filesystem;

class RegistrationHandler
{
    private $import_file;
    private $container;
    private $error_list;
    private $php_excel;
    private $model;
    private $filesystem;

    public function __construct(
        ContainerInterface $container,
        PHPExcelFactory $php_excel,
        RegistrationModel $model,
        Filesystem $filesystem
    ) {
        $this->container = $container;
        $this->php_excel = $php_excel;
        $this->model = $model;
        $this->filesystem = $filesystem;
        $this->error_list = array();
    }

    public function import(UploadedFile $file)
    {
        $this->uploadImportFile($file);
        $file_path = $this->container->getParameter('registration_import_file_upload_dir')
            . '/' . $file->getClientOriginalName();
        $file = fopen($file_path, "r");

        $data = array();
        while ($rec = fgetcsv($file)) {
            $data[] = $rec;
        }

        $this->model->save();
        $model_path = $this->container->getParameter("registration_model_dir")
            .'/'.($this->model)::FILE_NAME_AND_EXT;
        $model_file = fopen($model_path, "r");
        $model_data = array();
        while ($rec = fgetcsv($model_file)) {
            $model_data[] = $rec;
        }
        dump($model_data);

        fo


        $this->removeFile($file_path);
        $this->removeFile($model_path);
    }

    private function testBlankRow($worksheet, $row, $max_col)
    {
        for ($i = 0; $i <= $max_col; $i++) {
            if (!is_null($worksheet->getCellByColumnAndRow($i, $row)->getValue())) {
                return false;
            }
        }

        return true;
    }

    private function reportError($col, $row)
    {
        $string_position = \PHPExcel_Cell::stringFromColumnIndex($col) . $row;
        $error_message = 'Ligne:'.$row.';Col:'.($col+1).' ('.$string_position.')';
        array_push($this->error_list, $error_message);
    }

    private function uploadImportFile(UploadedFile $file)
    {
        $file->move(
            $this->container->getParameter('registration_import_file_upload_dir'),
            $file->getClientOriginalName()
        );
    }

    private function removeFile($file_path)
    {
        if ($this->filesystem->exists($file_path)) {
            $this->filesystem->remove($file_path);
        }
    }


}