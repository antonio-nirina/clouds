<?php
namespace AdminBundle\Service\FileHandler;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Filesystem\Filesystem;

class CSVHandler
{
    const MODE = "r";

    private $file_system;
    private $file_path;

    public function __construct(Filesystem $file_system)
    {
        $this->file_system = $file_system;
    }

    public function createArray($file_path)
    {
        $this->file_path = $file_path;
        if ($this->file_system->exists($file_path)) {
            $file = fopen($this->file_path, self::MODE);
            $data = array();
            while ($rec = fgetcsv($file)) {
                $data[] = $rec;
            }

            return $data;
        } else {
            return  null;
        }
    }

    public function areSameRows($to_compare, $row)
    {
        return $to_compare == $row ? true : false;
    }

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