<?php
namespace AdminBundle\Service\FileHandler;

use AdminBundle\Service\FileHandler\CSVHandler;

abstract class CSVFileContentBrowser
{
    public $rowIndex;
    public $dataSize;
    public $model;
    public $arrayModel;
    public $arrayData;
    public $csvHandler;

    /**
     * CSVFileContentBrowser constructor.
     * @param \AdminBundle\Service\FileHandler\CSVHandler $csvHandler
     */
    public function __construct(CSVHandler $csvHandler)
    {
        $this->csvHandler = $csvHandler;
        $this->arrayData = array();
        $this->arrayModel = array();
        $this->rowIndex = 0;
    }

    /**
     * @param $model
     * @param $arrayData
     */
    public function addData($model, $arrayData)
    {
        $this->model = $model;
        $this->arrayModel = $this->csvHandler->createArray($model->getSavePath());
        $this->arrayData = $arrayData;
        $this->dataSize = sizeof($this->arrayData);
    }

    /**
     * @return bool
     */
    public function increaseRowIndex()
    {
        if ($this->rowIndex < ($this->dataSize-1)) {
            $this->rowIndex++;
            return true;
        } else {
            return false;
        }
    }

    /**
     * restTobegin
     */
    public function resetToBegin()
    {
        $this->rowIndex = 0;
    }

    /**
     * row index csv
     */
    public function increaseRowIndexToNextNotBlankRow()
    {
        while ($this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])
            and $this->rowIndex < $this->dataSize
        ) {
            $this->rowIndex++;
        }
    }
}
