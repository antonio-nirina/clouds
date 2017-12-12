<?php
namespace AdminBundle\Service\ImportExport;

use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\ImportExport\SchemaChecker;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ResultSettingSchemaChecker extends ResultSettingValidator
{
    const ERROR_NO_USER_DATA_FOUND = "Pas de données de résultats trouvées";
    const ERROR_INCORRECT_HEADER = "En-tête(s) incorrect(s)";
    const ERROR_NO_USER_TITLE_FOUND = "Pas de titre de données de résultats trouvé";
    const ERROR_NO_USER_HEADER_FOUND = "Pas d'en-têtes de données de résultats trouvés";
    const ERROR_DUPLICATE_USER_DATA = "Existence de doublon non autorisé";
    const ERROR_EXISTENT_USER_WITH_EMAIL = "Adresse email déjà utilisée";
    
    private $user_data_title_row_index;
    private $user_data_header_row_index;
    private $user_data_first_row_index;

    public function __construct(
        CSVHandler $csv_handler,
        EntityManager $em,
        Container $container,
        ValidatorInterface $validator
    ) {
        parent::__construct($csv_handler, $em, $container, $validator);
        $this->container = $container;
    }

    private function checkDatas()
    {
        $program_manager = $this->container->get('admin.program');
        $program = $program_manager->getCurrent();

        $this->increaseRowIndexToNextNotBlankRow();
        if ($this->csv_handler->areSameRows(
            $this->array_model[$this->user_data_title_row_index],
            $this->array_data[$this->row_index]
        )) {
            if ($this->increaseRowIndex()) {
                if ($this->csv_handler->areSameRows(
                    $this->array_model[$this->user_data_header_row_index],
                    $this->array_data[$this->row_index]
                )) {
                    $header = $this->array_model[$this->user_data_header_row_index];

                    if ($this->increaseRowIndex()) {
                        if (!$this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                            $blank_row = false;
                            $this->user_data_first_row_index = $this->row_index;
                            while (!$blank_row) {
                                $error_list = $this->checkRowResult(
                                    $header,
                                    $this->array_data,
                                    $this->array_model,
                                    $this->user_data_header_row_index,
                                    $this->row_index,
                                    $program
                                );

                                if (!empty($error_list)) {
                                    return $error_list;
                                }

                                if ($this->increaseRowIndex()) {
                                    if ($this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                                        $blank_row = true;
                                    }
                                } else {
                                    break;
                                }
                            }
                        } else {
                            $this->addError($this->createErrorWithIndex(
                                self::ERROR_NO_USER_DATA_FOUND,
                                $this->row_index
                            ));
                            return $this->error_list;
                        }
                    } else {
                        $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_DATA_FOUND, $this->row_index));
                        return $this->error_list;
                    }
                } else {
                    $this->addError($this->createErrorWithIndex(self::ERROR_INCORRECT_HEADER, $this->row_index));
                    return $this->error_list;
                }
            } else {
                $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_HEADER_FOUND, $this->row_index));
                return $this->error_list;
            }
        } else {
            $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_TITLE_FOUND, $this->row_index));
            return $this->error_list;
        }

        return $this->error_list;
    }

    private function importDatas()
    {
        $this->resetToBegin();
        $program_manager = $this->container->get('admin.program');
        $program = $program_manager->getCurrent();

        $this->increaseRowIndexToNextNotBlankRow();
        if ($this->csv_handler->areSameRows(
            $this->array_model[$this->user_data_title_row_index],
            $this->array_data[$this->row_index]
        )) {
            if ($this->increaseRowIndex()) {
                if ($this->csv_handler->areSameRows(
                    $this->array_model[$this->user_data_header_row_index],
                    $this->array_data[$this->row_index]
                )) {
                    $header = $this->array_model[$this->user_data_header_row_index];

                    if ($this->increaseRowIndex()) {
                        if (!$this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                            $blank_row = false;
                            $this->user_data_first_row_index = $this->row_index;
                            while (!$blank_row) {
                                $error_list = $this->importRowResult(
                                    $header,
                                    $this->array_data,
                                    $this->array_model,
                                    $this->user_data_header_row_index,
                                    $this->row_index,
                                    $program
                                );

                                if (!empty($error_list)) {
                                    return $error_list;
                                }

                                if ($this->increaseRowIndex()) {
                                    if ($this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                                        $blank_row = true;
                                    }
                                } else {
                                    break;
                                }
                            }
                        } else {
                            $this->addError($this->createErrorWithIndex(
                                self::ERROR_NO_USER_DATA_FOUND,
                                $this->row_index
                            ));
                            return $this->error_list;
                        }
                    } else {
                        $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_DATA_FOUND, $this->row_index));
                        return $this->error_list;
                    }
                } else {
                    $this->addError($this->createErrorWithIndex(self::ERROR_INCORRECT_HEADER, $this->row_index));
                    return $this->error_list;
                }
            } else {
                $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_HEADER_FOUND, $this->row_index));
                return $this->error_list;
            }
        } else {
            $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_TITLE_FOUND, $this->row_index));
            return $this->error_list;
        }

        return $this->error_list;
    }

    public function import($model, $data)
    {
        $error_list = $this->importDatas();
        return array_unique($this->error_list);
    }

    public function check($model, $data)
    {
        parent::check($model, $data);
        
        if (count($this->model->getTitleRowIndexList()) > 0
            and count($this->model->getHeaderRowIndexList()) > 0
        ) {
            // 1-based index to 0-based - From PHPExcel index to CSV file index (by fgetcsv()
            $this->user_data_title_row_index = $this->model->getTitleRowIndexList()[0] - 1;
            $this->user_data_header_row_index = $this->model->getHeaderRowIndexList()[0] - 1;
        } else {
            $this->addError(SchemaChecker::ERROR_SCHEMA_CHECKER_INTERNAL_ERROR);
            return $this->error_list;
        }

        if (sizeof($this->array_data[0]) != sizeof($this->array_model[0])) {
            $this->addError(self::ERROR_WRONG_GENERAL_STRUCTURE);
            return $this->error_list;
        }

        if ($this->data_size <= 0) {
            $this->addError(self::ERROR_WRONG_GENERAL_STRUCTURE);
            return $this->error_list;
        }
        
        $error_list = $this->checkDatas();
        if (!empty($error_list)) {
            return $this->error_list;
        }

        // increasing $row_index
        // to find if another data after valid schema
        // add ERROR_INVALID_DATA if other data found
        if ($this->increaseRowIndex()) {
            $this->increaseRowIndexToNextNotBlankRow();
            if (!$this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                $this->addError($this->createErrorWithIndex(self::ERROR_INVALID_DATA, $this->row_index));
                return $this->error_list;
            }
        }

        return array_unique($this->error_list);
    }
}
