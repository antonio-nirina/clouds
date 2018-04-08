<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Service\FileHandler\CSVHandler;
use AdminBundle\Service\FileHandler\CSVFileContentBrowser;
use AdminBundle\Entity\SiteFormSetting;
use Doctrine\ORM\EntityManager;
use AdminBundle\Component\SiteForm\FieldType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;

class SchemaChecker extends CSVFileContentBrowser
{
    protected $error_list;
    protected $site_form_setting;
    protected $manager;
    protected $validator;

    const ERROR_SCHEMA_CHECKER_INTERNAL_ERROR = "Erreur interne, vérification du dessin d'enregistrement.";
    const ERROR_NO_ASSOCIATED_FORM = "Erreur interne. Pas de formulaire associé.";
    const ERROR_NO_ASSOCIATED_FIELD = "Erreur interne. Pas de champ associé.";
    const ERROR_INVALID_DATA = "Donnée invalide.";
    const ERROR_NO_HEADER_FOUND = "Pas d'entêtes trouvées.";
    const ERROR_NO_DATA_FOUND = "Pas de données trouvées.";
    const ERROR_WRONG_GENERAL_STRUCTURE = "Structure générale incorrect.";
    const ERROR_INVALID_DATA_FORMAT = "Format de donnée invalide.";
    const ERROR_MISSING_VALUE_ON_MANDATORY_FIELD = "Donnée absent sur champ obligatoire.";

    public function __construct(CSVHandler $csv_handler, EntityManager $manager, ValidatorInterface $validator)
    {
        parent::__construct($csv_handler);

        $this->csv_handler = $csv_handler;
        $this->manager = $manager;
        $this->error_list = array();
        $this->validator = $validator;
    }

    public function setSiteFormSetting(SiteFormSetting $site_form_setting)
    {
        $this->site_form_setting = $site_form_setting;
    }

    protected function addError($error)
    {
        array_push($this->error_list, $error);
        return;
    }

    protected function removeError($error)
    {
        foreach (array_keys($this->error_list, $error) as $key) {
            unset($this->error_list[$key]);
        }
        return;
    }

    protected function createErrorWithIndex($error_message, $row_index, $col_index = null)
    {
        $message = $error_message . ' Ligne: ' . ($row_index+1); // 0-based index to 1-based index (human readable)
        return is_null($col_index)
            ? $message
            : $message . ', Colonne: ' . ($col_index+1); // 0-based index to 1-based index (human readable)
    }

    protected function checkRow($array_data, $array_model, $header_row_index, $row_index)
    {
        if (is_null($this->site_form_setting)) {
            $this->addError(self::ERROR_NO_ASSOCIATED_FORM);

            return $this->error_list;
        } else {
            $i = $row_index;
            foreach ($array_data[$i] as $key => $col_element) {
                if (!empty($array_model[$header_row_index][$key])) {
                    $related_field_setting = $this->manager
                        ->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                        ->findBySiteFormSettingAndLabel(
                            $this->site_form_setting,
                            $array_model[$header_row_index][$key]
                        );
                    if (is_null($related_field_setting)) {
                        $this->addError(
                            $this->createErrorWithIndex(
                                SchemaChecker::ERROR_NO_ASSOCIATED_FIELD,
                                $i,
                                $key
                            )
                        );
                        return $this->error_list;
                    }

                    // check mandatory state
                    if (!empty($this->checkMandatoryData($col_element, $related_field_setting, $i, $key))) {
                        return $this->error_list;
                    }

                    // check format
                    if (!empty($this->checkDataFormat($col_element, $related_field_setting, $i, $key))) {
                        return $this->error_list;
                    }
                }
            }

            return array();
        }
    }

    protected function checkMandatoryData($col_element, $related_field_setting, $row_index, $col_index)
    {
        if (true == $related_field_setting->getMandatory()
            && empty($col_element)
        ) {
            $this->addError(
                $this->createErrorWithIndex(
                    SchemaChecker::ERROR_MISSING_VALUE_ON_MANDATORY_FIELD,
                    $row_index,
                    $col_index
                )
            );
            return $this->error_list;
        }
        return array();
    }

    protected function checkDataFormat($col_element, $related_field_setting, $row_index, $col_index)
    {
        switch ($related_field_setting->getFieldType()) {
            case FieldType::TEXT:
                return array();
                break;
            case FieldType::ALPHA_TEXT:
                return $this->validateColumnElement(
                    $col_element,
                    new Type(array("type" => "alpha")),
                    $row_index,
                    $col_index
                );
                break;
            case FieldType::NUM_TEXT:
                return $this->validateColumnElement(
                    $col_element,
                    new Type(array("type" => "numeric")),
                    $row_index,
                    $col_index
                );
                break;
            case FieldType::ALPHANUM_TEXT:
                return $this->validateColumnElement(
                    $col_element,
                    new Type(array("type" => "alnum")),
                    $row_index,
                    $col_index
                );
                break;
            case FieldType::EMAIL:
                return $this->validateColumnElement(
                    $col_element,
                    new Email(),
                    $row_index,
                    $col_index
                );
                break;
        }
    }

    protected function validateColumnElement(
        $col_element,
        $type,
        $row_index,
        $col_index,
        $error_if_not_valid = self::ERROR_INVALID_DATA_FORMAT
    ) {
        $violations = $this->validator->validate($col_element, $type);
        if (0 !== count($violations)) {
            $this->addError(
                $this->createErrorWithIndex(
                    $error_if_not_valid,
                    $row_index,
                    $col_index
                )
            );
            return $this->error_list;
        }
        return array();
    }

    public function check($model, $data)
    {
        $this->addData($model, $data);
        return $this->error_list;
    }
}
