<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\ImportExport\SchemaChecker;
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

    private $userDataTitleRowIndex;
    private $userDataHeaderRowIndex;
    private $userDataFirstRowIndex;

    public function __construct(
        CSVHandler $csvHandler,
        EntityManager $em,
        Container $container,
        ValidatorInterface $validator
    ) {
        parent::__construct($csvHandler, $em, $container, $validator);
        $this->container = $container;
    }

    private function checkDatas()
    {
        $programManager = $this->container->get('admin.program');
        $program = $programManager->getCurrent();

        $this->increaseRowIndexToNextNotBlankRow();
        if ($this->csvHandler->areSameRows(
            $this->arrayModel[$this->userDataTitleRowIndex],
            $this->arrayData[$this->rowIndex]
        )
        ) {
            if ($this->increaseRowIndex()) {
                if ($this->csvHandler->areSameRows(
                    $this->arrayModel[$this->userDataHeaderRowIndex],
                    $this->arrayData[$this->rowIndex]
                )
                ) {
                    $header = $this->arrayModel[$this->userDataHeaderRowIndex];

                    if ($this->increaseRowIndex()) {
                        if (!$this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])) {
                            $blankRow = false;
                            $this->userDataFirstRowIndex = $this->rowIndex;
                            while (!$blankRow) {
                                $errorList = $this->checkRowResult(
                                    $header,
                                    $this->arrayData,
                                    $this->arrayModel,
                                    $this->userDataHeaderRowIndex,
                                    $this->rowIndex,
                                    $program
                                );

                                if (!empty($errorList)) {
                                    return $errorList;
                                }

                                if ($this->increaseRowIndex()) {
                                    if ($this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])) {
                                        $blankRow = true;
                                    }
                                } else {
                                    break;
                                }
                            }
                        } else {
                            $this->addError(
                                $this->createErrorWithIndex(
                                    self::ERROR_NO_USER_DATA_FOUND,
                                    $this->rowIndex
                                )
                            );
                            return $this->errorList;
                        }
                    } else {
                        $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_DATA_FOUND, $this->rowIndex));
                        return $this->errorList;
                    }
                } else {
                    $this->addError($this->createErrorWithIndex(self::ERROR_INCORRECT_HEADER, $this->rowIndex));
                    return $this->errorList;
                }
            } else {
                $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_HEADER_FOUND, $this->rowIndex));
                return $this->errorList;
            }
        } else {
            $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_TITLE_FOUND, $this->rowIndex));
            return $this->errorList;
        }

        return $this->errorList;
    }

    private function importDatas()
    {
        $this->resetToBegin();
        $programManager = $this->container->get('admin.program');
        $program = $programManager->getCurrent();

        $this->increaseRowIndexToNextNotBlankRow();
        if ($this->csvHandler->areSameRows(
            $this->arrayModel[$this->userDataTitleRowIndex],
            $this->arrayData[$this->rowIndex]
        )
        ) {
            if ($this->increaseRowIndex()) {
                if ($this->csvHandler->areSameRows(
                    $this->arrayModel[$this->userDataHeaderRowIndex],
                    $this->arrayData[$this->rowIndex]
                )
                ) {
                    $header = $this->arrayModel[$this->userDataHeaderRowIndex];

                    if ($this->increaseRowIndex()) {
                        if (!$this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])) {
                            $blankRow = false;
                            $this->userDataFirstRowIndex = $this->rowIndex;
                            while (!$blankRow) {
                                $errorList = $this->importRowResult(
                                    $header,
                                    $this->arrayData,
                                    $this->arrayModel,
                                    $this->userDataHeaderRowIndex,
                                    $this->rowIndex,
                                    $program
                                );

                                if (!empty($errorList)) {
                                    return $errorList;
                                }

                                if ($this->increaseRowIndex()) {
                                    if ($this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])) {
                                        $blankRow = true;
                                    }
                                } else {
                                    break;
                                }
                            }
                        } else {
                            $this->addError(
                                $this->createErrorWithIndex(
                                    self::ERROR_NO_USER_DATA_FOUND,
                                    $this->rowIndex
                                )
                            );
                            return $this->errorList;
                        }
                    } else {
                        $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_DATA_FOUND, $this->rowIndex));
                        return $this->errorList;
                    }
                } else {
                    $this->addError($this->createErrorWithIndex(self::ERROR_INCORRECT_HEADER, $this->rowIndex));
                    return $this->errorList;
                }
            } else {
                $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_HEADER_FOUND, $this->rowIndex));
                return $this->errorList;
            }
        } else {
            $this->addError($this->createErrorWithIndex(self::ERROR_NO_USER_TITLE_FOUND, $this->rowIndex));
            return $this->errorList;
        }

        return $this->errorList;
    }

    public function import($model, $data)
    {
        $errorList = $this->importDatas();
        return array_unique($this->errorList);
    }

    public function check($model, $data)
    {
        parent::check($model, $data);

        if (count($this->model->getTitleRowIndexList()) > 0
            && count($this->model->getHeaderRowIndexList()) > 0
        ) {
            // 1-based index to 0-based - From PHPExcel index to CSV file index (by fgetcsv()
            $this->userDataTitleRowIndex = $this->model->getTitleRowIndexList()[0] - 1;
            $this->userDataHeaderRowIndex = $this->model->getHeaderRowIndexList()[0] - 1;
        } else {
            $this->addError(SchemaChecker::ERROR_SCHEMA_CHECKER_INTERNAL_ERROR);
            return $this->errorList;
        }

        if (sizeof($this->arrayData[0]) != sizeof($this->arrayModel[0])) {
            $this->addError(self::ERROR_WRONG_GENERAL_STRUCTURE);
            return $this->errorList;
        }

        if ($this->data_size <= 0) {
            $this->addError(self::ERROR_WRONG_GENERAL_STRUCTURE);
            return $this->errorList;
        }

        $errorList = $this->checkDatas();
        if (!empty($errorList)) {
            return $this->errorList;
        }

        // increasing $rowIndex
        // to find if another data after valid schema
        // add ERROR_INVALID_DATA if other data found
        if ($this->increaseRowIndex()) {
            $this->increaseRowIndexToNextNotBlankRow();
            if (!$this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])) {
                $this->addError($this->createErrorWithIndex(self::ERROR_INVALID_DATA, $this->rowIndex));
                return $this->errorList;
            }
        }

        return array_unique($this->errorList);
    }
}
