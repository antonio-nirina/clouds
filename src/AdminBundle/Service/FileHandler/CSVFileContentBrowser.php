<?php
namespace AdminBundle\Service\FileHandler;

use AdminBundle\Service\FileHandler\CSVHandler;

abstract class CSVFileContentBrowser
{
    public $row_index;
    public $data_size;
    public $model;
    public $array_model;
    public $array_data;
    public $csv_handler;

    public function __construct(CSVHandler $csv_handler)
    {
        $this->csv_handler = $csv_handler;
        $this->array_data = array();
        $this->array_model = array();
        $this->row_index = 0;
    }

    public function addData($model, $array_data)
    {
        $this->model = $model;
        $this->array_model = $this->csv_handler->createArray($model->getSavePath());
        $this->array_data = $array_data;
        $this->data_size = sizeof($this->array_data);
    }

    public function increaseRowIndex()
    {
        if ($this->row_index < ($this->data_size-1)) {
            $this->row_index++;
            return true;
        } else {
            return false;
        }
    }

    public function resetToBegin()
    {
        $this->row_index = 0;
    }

    public function increaseRowIndexToNextNotBlankRow()
    {
        while ($this->csv_handler->isBlankRow($this->array_data[$this->row_index])
            and $this->row_index < $this->data_size
        ) {
            $this->row_index++;
        }
    }
}
