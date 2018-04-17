<?php

namespace AdminBundle\Service\FileHandler;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Filesystem\Filesystem;

class CSVHandler
{
    const MODE = "r";

    private $fileSystem;
    private $filePath;

    /**
     * CSVHandler constructor.
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param $filePath
     * @return array|null
     */
    public function createArray($filePath)
    {
        $this->filePath = $filePath;
        if ($this->fileSystem->exists($filePath)) {
            $file = fopen($this->filePath, self::MODE);
            $data = array();
            while ($rec = fgetcsv($file, 0, ';')) {
                $data[] = $rec;
            }

            return $data;
        } else {
            return  null;
        }
    }

    /**
     * @param $toCompare
     * @param $row
     * @return bool
     */
    public function areSameRows($toCompare, $row)
    {
        return $toCompare == $row ? true : false;
    }

    /**
     * @param $row
     * @return bool
     */
    public function isBlankRow($row)
    {
        foreach ($row as $col_el) {
            if ("" != $col_el) {
                return false;
            }
        }

        return true;
    }
}
