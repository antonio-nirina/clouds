<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\ImportExport\SchemaChecker;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

class RegistrationSchemaChecker extends SchemaChecker
{
    const ERROR_NO_COMPANY_DATA_FOUND = "Pas de données de société trouvées.";
    const ERROR_NO_USER_DATA_FOUND = "Pas de données de participant trouvées";
    const ERROR_INCORRECT_HEADER = "En-tête(s) incorrect(s)";
    const ERROR_NOT_UNIQUE_COMPANY_DATA = "La ligne de données de société doit être unique";
    const ERROR_NO_COMPANY_TITLE_FOUND = "Pas de titre de données de société trouvé";
    const ERROR_NO_COMPANY_HEADER_FOUND = "Pas d'en-têtes de données de société trouvés";
    const ERROR_NO_USER_TITLE_FOUND = "Pas de titre de données de participant trouvé";
    const ERROR_NO_USER_HEADER_FOUND = "Pas d'en-têtes de données de participant trouvés";
    const ERROR_DUPLICATE_USER_DATA = "Existence de doublon non autorisé";
    const ERROR_EXISTENT_USER_WITH_EMAIL = "Adresse email déjà utilisée";

    private $company_data_title_row_index;
    private $user_data_title_row_index;
    private $company_data_header_row_index;
    private $user_data_header_row_index;
    private $user_data_first_row_index;

    private function checkCompanyDatas()
    {
        $this->increaseRowIndexToNextNotBlankRow();
        if ($this->csv_handler->areSameRows(
            $this->array_model[$this->company_data_title_row_index],
            $this->array_data[$this->row_index]
        )) {
            if ($this->increaseRowIndex()) {
                if ($this->csv_handler->areSameRows(
                    $this->array_model[$this->company_data_header_row_index],
                    $this->array_data[$this->row_index]
                )) {
                    if ($this->increaseRowIndex()) {
                        if (!$this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                            $error_list = $this->checkRow(
                                $this->array_data,
                                $this->array_model,
                                $this->company_data_header_row_index,
                                $this->row_index
                            );

                            if (!empty($error_list)) {
                                return $error_list;
                            }

                            if ($this->increaseRowIndex()) {
                                if (!$this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                                    $this->addError($this->createErrorWithIndex(
                                        self::ERROR_NOT_UNIQUE_COMPANY_DATA,
                                        $this->row_index
                                    ));
                                    return $this->error_list;
                                }
                            }
                        } else {
                            $this->addError($this->createErrorWithIndex(
                                self::ERROR_NO_COMPANY_DATA_FOUND,
                                $this->row_index
                            ));
                            return $this->error_list;
                        }
                    } else {
                        $this->addError($this->createErrorWithIndex(
                            self::ERROR_NO_COMPANY_DATA_FOUND,
                            $this->row_index
                        ));
                        return $this->error_list;
                    }

                } else {
                    $this->addError($this->createErrorWithIndex(self::ERROR_INCORRECT_HEADER, $this->row_index));
                    return $this->error_list;
                }
            } else {
                $this->addError($this->createErrorWithIndex(self::ERROR_NO_COMPANY_HEADER_FOUND, $this->row_index));
                return $this->error_list;
            }
        } else {
            $this->addError($this->createErrorWithIndex(self::ERROR_NO_COMPANY_TITLE_FOUND, $this->row_index));
            return $this->error_list;
        }

        return $this->error_list;
    }

    private function checkUserDatas()
    {
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
                    if ($this->increaseRowIndex()) {
                        if (!$this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                            $blank_row = false;
                            $this->user_data_first_row_index = $this->row_index;
                            while (!$blank_row) {
                                $error_list = $this->checkRow(
                                    $this->array_data,
                                    $this->array_model,
                                    $this->user_data_header_row_index,
                                    $this->row_index
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

    public function check($model, $data)
    {
        parent::check($model, $data);

        if (array_key_exists(0, $this->model->getTitleRowIndexList())
            and array_key_exists(1, $this->model->getTitleRowIndexList())
            and array_key_exists(0, $this->model->getHeaderRowIndexList())
            and array_key_exists(1, $this->model->getHeaderRowIndexList())
        ) {
            // 1-based index to 0-based - From PHPExcel index to CSV file index (by fgetcsv()
            $this->company_data_title_row_index = $this->model->getTitleRowIndexList()[0] - 1;
            $this->user_data_title_row_index = $this->model->getTitleRowIndexList()[1] - 1;
            $this->company_data_header_row_index = $this->model->getHeaderRowIndexList()[0] - 1;
            $this->user_data_header_row_index = $this->model->getHeaderRowIndexList()[1] - 1;
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

        $error_list = $this->checkCompanyDatas();
        if (!empty($error_list)) {
            return $this->error_list;
        }
        $error_list = $this->checkUserDatas();
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

        // check if there are same email
        // or mail already used in application
        $email_field = $this->manager->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
            ->findOneBy(array(
                'site_form_setting' => $this->site_form_setting,
                'special_field_index' => SpecialFieldIndex::USER_EMAIL,
                'published' => true,
            ));

        if (in_array($email_field->getLabel(), $this->array_model[$this->user_data_header_row_index])) {
            $email_field_col_index = array_keys(
                $this->array_model[$this->user_data_header_row_index],
                $email_field->getLabel()
            )[0];
            if (!is_null($this->user_data_first_row_index)) {
                $user_email_list = array();
                for ($i = $this->user_data_first_row_index; $i < $this->data_size; $i++) {
                    if ('' != trim($this->array_data[$i][$email_field_col_index])) {
                        array_push($user_email_list, $this->array_data[$i][$email_field_col_index]);
                    }
                }

                if (count(array_unique($user_email_list)) < count($user_email_list)) {
                    $this->addError(self::ERROR_DUPLICATE_USER_DATA.', colonne "'.$email_field->getLabel().'"');
                    return $this->error_list;
                }

                for ($i = $this->user_data_first_row_index; $i < $this->data_size; $i++) {
                    // SEARCH CRITERIAS MUST BE INCREASED : ADD program, program_user linked to user
                    $user_with_email = $this->manager->getRepository('UserBundle\Entity\User')
                        ->findOneByEmail($this->array_data[$i][$email_field_col_index]);
                    if ('' != trim($this->array_data[$i][$email_field_col_index])) {
                        if (!is_null($user_with_email)) {
                            $this->addError($this->createErrorWithIndex(
                                self::ERROR_EXISTENT_USER_WITH_EMAIL,
                                $i,
                                $email_field_col_index
                            ));
                            return $this->error_list;
                        }
                    }
                }
            } else {
                $this->addError(self::ERROR_NO_USER_DATA_FOUND);
                return $this->error_list;
            }
        }

        return array_unique($this->error_list);
    }
}