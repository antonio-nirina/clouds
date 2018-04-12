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
    protected $errorList;
    protected $siteFormSetting;
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

    public function __construct(CSVHandler $csvHandler, EntityManager $manager, ValidatorInterface $validator)
    {
        parent::__construct($csvHandler);

        $this->csvHandler = $csvHandler;
        $this->manager = $manager;
        $this->errorList = array();
        $this->validator = $validator;
    }

    public function setSiteFormSetting(SiteFormSetting $siteFormSetting)
    {
        $this->siteFormSetting = $siteFormSetting;
    }

    protected function addError($error)
    {
        array_push($this->errorList, $error);
        return;
    }

    protected function removeError($error)
    {
        foreach (array_keys($this->errorList, $error) as $key) {
            unset($this->errorList[$key]);
        }
        return;
    }

    protected function createErrorWithIndex($errorMessage, $rowIndex, $colIndex = null)
    {
        $message = $errorMessage . ' Ligne: ' . ($rowIndex+1); // 0-based index to 1-based index (human readable)
        return is_null($colIndex)
            ? $message
            : $message . ', Colonne: ' . ($colIndex+1); // 0-based index to 1-based index (human readable)
    }

    protected function checkRow($arrayData, $arrayModel, $headerRowIndex, $rowIndex)
    {
        if (is_null($this->siteFormSetting)) {
            $this->addError(self::ERROR_NO_ASSOCIATED_FORM);

            return $this->errorList;
        } else {
            $i = $rowIndex;
            foreach ($arrayData[$i] as $key => $colElement) {
                if (!empty($arrayModel[$headerRowIndex][$key])) {
                    $relatedFieldSetting = $this->manager
                        ->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                        ->findBySiteFormSettingAndLabel(
                            $this->siteFormSetting,
                            $arrayModel[$headerRowIndex][$key]
                        );
                    if (is_null($relatedFieldSetting)) {
                        $this->addError(
                            $this->createErrorWithIndex(
                                SchemaChecker::ERROR_NO_ASSOCIATED_FIELD,
                                $i,
                                $key
                            )
                        );
                        return $this->errorList;
                    }

                    // check mandatory state
                    if (!empty($this->checkMandatoryData($colElement, $relatedFieldSetting, $i, $key))) {
                        return $this->errorList;
                    }

                    // check format
                    if (!empty($this->checkDataFormat($colElement, $relatedFieldSetting, $i, $key))) {
                        return $this->errorList;
                    }
                }
            }

            return array();
        }
    }

    protected function checkMandatoryData($colElement, $relatedFieldSetting, $rowIndex, $colIndex)
    {
        if (true == $relatedFieldSetting->getMandatory()
            && empty($colElement)
        ) {
            $this->addError(
                $this->createErrorWithIndex(
                    SchemaChecker::ERROR_MISSING_VALUE_ON_MANDATORY_FIELD,
                    $rowIndex,
                    $colIndex
                )
            );
            return $this->errorList;
        }
        return array();
    }

    protected function checkDataFormat($colElement, $relatedFieldSetting, $rowIndex, $colIndex)
    {
        switch ($relatedFieldSetting->getFieldType()) {
            case FieldType::TEXT:
                return array();
                break;
            case FieldType::ALPHA_TEXT:
                return $this->validateColumnElement(
                    $colElement,
                    new Type(array("type" => "alpha")),
                    $rowIndex,
                    $colIndex
                );
                break;
            case FieldType::NUM_TEXT:
                return $this->validateColumnElement(
                    $colElement,
                    new Type(array("type" => "numeric")),
                    $rowIndex,
                    $colIndex
                );
                break;
            case FieldType::ALPHANUM_TEXT:
                return $this->validateColumnElement(
                    $colElement,
                    new Type(array("type" => "alnum")),
                    $rowIndex,
                    $colIndex
                );
                break;
            case FieldType::EMAIL:
                return $this->validateColumnElement(
                    $colElement,
                    new Email(),
                    $rowIndex,
                    $colIndex
                );
                break;
        }
    }

    protected function validateColumnElement(
        $colElement,
        $type,
        $rowIndex,
        $colIndex,
        $errorIfNotValid = self::ERROR_INVALID_DATA_FORMAT
    ) {
        $violations = $this->validator->validate($colElement, $type);
        if (0 !== count($violations)) {
            $this->addError(
                $this->createErrorWithIndex(
                    $errorIfNotValid,
                    $rowIndex,
                    $colIndex
                )
            );
            return $this->errorList;
        }
        return array();
    }

    public function check($model, $data)
    {
        $this->addData($model, $data);
        return $this->errorList;
    }
}
