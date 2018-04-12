<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Component\SiteForm\FieldType;
use AdminBundle\Entity\Role;
use AdminBundle\Entity\Sales;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Manager\ProgramManager;
use AdminBundle\Service\FileHandler\CSVFileContentBrowser;
use AdminBundle\Service\FileHandler\CSVHandler;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResultSettingValidator extends CSVFileContentBrowser
{
    protected $errorList;
    protected $siteFormSetting;
    protected $manager;
    protected $validator;
    protected $container;

    const ERROR_SCHEMA_CHECKER_INTERNAL_ERROR = "Erreur interne, vérification du dessin d'enregistrement";
    const ERROR_NO_ASSOCIATED_FORM = "Erreur interne. Pas de formulaire associé";
    const ERROR_NO_ASSOCIATED_FIELD = "Erreur interne. Pas de champ associé";
    const ERROR_INVALID_DATA = "Donnée invalide.";
    const ERROR_NO_HEADER_FOUND = "Pas d'entêtes trouvées";
    const ERROR_NO_DATA_FOUND = "Pas de données trouvées";
    const ERROR_WRONG_GENERAL_STRUCTURE = "Structure générale incorrect";
    const ERROR_INVALID_DATA_FORMAT = "Format de donnée invalide";
    const ERROR_MISSING_VALUE_ON_MANDATORY_FIELD = "Donnée absent sur champ obligatoire";
    const ERROR_MISSING_VALUE_ON_PRODUCT_FIELD = 'Au moins une des colonnes "Produit" doit être remplie';
    const ERROR_USER_NOT_FOUND = "Utilisateur pas enregistré, veuillez l'inscrire";
    const ERROR_WRONG_DATE_FOMAT = "Mauvais format de date, mettre en JJ/MM/AAAA";
    const ERROR_WRONG_NUM_FORMAT = "Fournisser une valeur numérique";
    const ERROR_WRONG_INTEGER_FORMAT = "Fournisser une valeur entre 1 à 6";
    const ERROR_WRONG_ALPHANUM_FORMAT = "Fournisser une valeur alphanumérique";
    const ERROR_WRONG_TEXT_FORMAT = "Fournisser une valeur texte";


    /**
     * ResultSettingValidator constructor.
     * @param CSVHandler $csvHandler
     * @param EntityManager $manager
     * @param Container $container
     * @param ValidatorInterface $validator
     */
    public function __construct(
        CSVHandler $csvHandler,
        EntityManager $manager,
        Container $container,
        ValidatorInterface $validator
    ) {
        parent::__construct($csvHandler);

        $this->csvHandler = $csvHandler;
        $this->manager = $manager;
        $this->errorList = array();
        $this->validator = $validator;
        $this->container = $container;
    }

    /**
     * @param SiteFormSetting $siteFormSetting
     */
    public function setSiteFormSetting(SiteFormSetting $siteFormSetting)
    {
        $this->siteFormSetting = $siteFormSetting;
    }

    /**
     * @param $error
     */
    protected function addError($error)
    {
        array_push($this->errorList, $error);
        return;
    }

    /**
     * @param $error
     */
    protected function removeError($error)
    {
        foreach (array_keys($this->errorList, $error) as $key) {
            unset($this->errorList[$key]);
        }
        return;
    }

    /**
     * @param $errorMessage
     * @param $rowIndex
     * @param null $colIndex
     * @return string
     */
    protected function createErrorWithIndex($errorMessage, $rowIndex, $colIndex = null)
    {
        $message = $errorMessage . ', Ligne: ' . ($rowIndex+1); // 0-based index to 1-based index (human readable)
        return is_null($colIndex)
            ? $message
            : $message . ', Colonne: ' . ($colIndex+1); // 0-based index to 1-based index (human readable)
    }

    /**
     * @param $errorMessage
     * @param $rowIndex
     * @param $colIndex
     * @return string
     */
    protected function createErrorWithColumn($errorMessage, $rowIndex, $colIndex)
    {
        $message = $errorMessage . ', Ligne: ' . ($rowIndex+1); // 0-based index to 1-based index (human readable)
        return is_null($colIndex)
            ? $message
            : $message . ', Colonne: "' . ($colIndex) . '"'; // 0-based index to 1-based index (human readable)
    }

    /**
     * @param $header
     * @param $arrayData
     * @param $arrayModel
     * @param $headerRowIndex
     * @param $rowIndex
     * @param $program
     * @return array
     */
    protected function checkRowResult($header, $arrayData, $arrayModel, $headerRowIndex, $rowIndex, $program)
    {
        $i = $rowIndex;
        $currentRow = array_combine($header, $arrayData[$i]);

        //date check
        $prodEmpty = true;
        foreach ($currentRow as $index => $col) {
            //impérative
            if (in_array($index, array("Nom", "Prénom", "Fonction"/*,"Rang","Réseau"*/))) {
                if (empty($col)) {
                    $this->addError(
                        $this->createErrorWithColumn(
                            SchemaChecker::ERROR_MISSING_VALUE_ON_MANDATORY_FIELD,
                            $rowIndex,
                            $index
                        )
                    );
                }
            }
            if ($prodEmpty) {//TEST CHIFFRE D'AFFAIRE VIDE
                for ($j=1; $j < 5; $j++) {
                    if ($index == "Produit $j") {
                        if (empty($col)) {
                            $prodEmpty = true;
                        } else {
                            $prodEmpty = false;
                            break;
                        }
                    }
                }
            }
            //check format des données
            if (in_array($index, array('Période de', 'à', 'Date'))
            ) {//check date
                $reg_exp = "#(^(((0[1-9]|[12][0-8])[\/](0[1-9]|1[012]))|((29|30|31)[\/](0[13578]|1[02]))|((29|30)[\/](0[4,6,9]|11)))[\/](19|[2-9][0-9])\d\d$)|(^29[\/]02[\/](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)#";

                $this->validateColumnElement2(
                    $currentRow[$index],
                    new Regex(array("pattern" => $reg_exp)),
                    $i,
                    $index,
                    self::ERROR_WRONG_DATE_FOMAT
                );

                // preg_match($reg_exp, $currentRow[$index], $date);
                // if (empty($date)) {
                //     $this->addError(
                //         $this->createErrorWithColumn(
                //             self::ERROR_WRONG_DATE_FOMAT,
                //             $i,
                //             $index
                //         )
                //     );
                // }
            } elseif (!empty($currentRow[$index])
                && (                in_array($index, array('Rang')))
            ) { //check integer
                $this->validateColumnElement2(
                    $currentRow[$index],
                    new Regex(array("pattern" => "#(^[1-6]$)#")),
                    $i,
                    $index,
                    self::ERROR_WRONG_INTEGER_FORMAT
                );
                // preg_match("#(^[1-6]$)#", $currentRow[$index], $num);
                // if (empty($num)) {
                //     $this->addError(
                //         $this->createErrorWithColumn(
                //             self::ERROR_WRONG_INTEGER_FORMAT,
                //             $i,
                //             $index
                //         )
                //     );
                // }
            } elseif (!empty($currentRow[$index])
                && (                strpos($index, 'Produit') !== false)
            ) { //check numeric
                $this->validateColumnElement2(
                    $currentRow[$index],
                    new Type(array("type" => "numeric")),
                    $i,
                    $index,
                    self::ERROR_WRONG_NUM_FORMAT
                );
            } elseif (!empty($currentRow[$index])) { //alphanumeric
                $this->validateColumnElement2(
                    $currentRow[$index],
                    new Type(array("type" => "string")),
                    $i,
                    $index,
                    self::ERROR_WRONG_ALPHANUM_FORMAT
                );
            }
        }

        if ($prodEmpty) {
            $this->addError(
                $this->createErrorWithIndex(
                    self::ERROR_MISSING_VALUE_ON_PRODUCT_FIELD,
                    $i
                )
            );
        }
        //user check
        /*$user = $this->manager
            ->getRepository('AdminBundle\Entity\ProgramUser')
            ->findByNameAndLastName(
                $currentRow['Nom'],
                $currentRow['Prénom'],
                $program
            );*/
        // check user by ID, instead of name and lastname
        $user = $this->manager
            ->getRepository('AdminBundle\Entity\ProgramUser')
            ->findOneBy(array(
                'id' => $currentRow['ID'],
                'program' => $program
            ));
        if (empty($user)) {
            $this->addError(
                $this->createErrorWithIndex(
                    self::ERROR_USER_NOT_FOUND,
                    $i
                )
            );
        }

        return $this->errorList;
    }

    /**
     * @param $header
     * @param $arrayData
     * @param $arrayModel
     * @param $headerRowIndex
     * @param $rowIndex
     * @param $program
     */
    protected function importRowResult($header, $arrayData, $arrayModel, $headerRowIndex, $rowIndex, $program)
    {
        $i = $rowIndex;
        $currentRow = array_combine($header, $arrayData[$i]);

        //user check
        /*$program_user = $this->manager
            ->getRepository('AdminBundle\Entity\ProgramUser')
            ->findByNameAndLastName(
                $currentRow['Nom'],
                $currentRow['Prénom'],
                $program
            );
        $program_user = $program_user[0];*/
        // check user by ID, instead of name and lastname
        $program_user = $this->manager
            ->getRepository('AdminBundle\Entity\ProgramUser')
            ->findOneBy(array(
                'id' => $currentRow['ID'],
                'program' => $program
            ));

        if (array_key_exists("Fonction", $currentRow)) { //assignation role commercial
            $role = $this->manager
                ->getRepository('AdminBundle\Entity\Role')
                ->findBy(
                    array(
                                'name' => $currentRow['Fonction'],
                                'program' => $program
                            )
                );

            if (!empty($role)) {
                $role = $role[0];
            } else {
                $role = new Role();
                $role->setName($currentRow['Fonction']);
                if ($currentRow['Rang']) {
                    $role->setRank($currentRow['Rang']);
                }
                // $role->setNetwork($currentRow['Réseau']);
                $role->setProgram($program);
                $this->manager->persist($role);
                $this->manager->flush();
            }

            $program_user->setRole($role);
        }

        $salesPointAttribution = $this->container->get('AdminBundle\Service\PointAttribution\SalesPointAttribution');
        for ($i=1; $i < 5; $i++) { //insertion des ventes
            if (array_key_exists("Produit $i", $currentRow) && !empty($currentRow["Produit $i"])) {
                $sales = new Sales();
                $sales->setCa($currentRow["Produit $i"]);
                $sales->setProgramUser($program_user);
                $sales->setProductGroup($i);

                if (array_key_exists("Dénomination $i", $currentRow)) {
                    $sales->setProductName($currentRow["Dénomination $i"]);
                } else {
                    $sales->setProductName("Produit $i");
                }

                $format = 'd/m/Y H:i:s';
                if (array_key_exists("Date", $currentRow)) {
                    $sales->setDate(\DateTime::createFromFormat($format, $currentRow["Date"] . " 00:00:00"));
                }

                if (array_key_exists("Période de", $currentRow)) {
                    $sales->setDateFrom(\DateTime::createFromFormat($format, $currentRow["Période de"] . " 00:00:00"));
                    $sales->setDateTo(\DateTime::createFromFormat($format, $currentRow["à"] . " 23:59:59"));
                }

                $this->manager->persist($sales);
                $this->manager->flush();

                /* définition des points pour mise à jour des points */
                $salesPointAttribution->attributedByProduct($sales); //by product and by period
                $salesPointAttribution->updateUserClassmentPerformance($sales); //update performance
                if (array_key_exists("Fonction", $currentRow)) {//by rank
                    $salesPointAttribution->attributedByRank($sales);
                }
            }
        }
    }

    /**
     * @param $colElement
     * @param $type
     * @param $rowIndex
     * @param $colIndex
     * @param string $errorIfNotValid
     * @return array
     */
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

    /**
     * @param $colElement
     * @param $type
     * @param $rowIndex
     * @param $colIndex
     * @param string $errorIfNotValid
     * @return array
     */
    protected function validateColumnElement2(
        $colElement,
        $type,
        $rowIndex,
        $colIndex,
        $errorIfNotValid = self::ERROR_INVALID_DATA_FORMAT
    ) {
        $violations = $this->validator->validate($colElement, $type);
        if (0 !== count($violations)) {
            $this->addError(
                $this->createErrorWithColumn(
                    $errorIfNotValid,
                    $rowIndex,
                    $colIndex
                )
            );
            return $this->errorList;
        }
        return array();
    }

    /**
     * @param $model
     * @param $data
     * @return array
     */
    public function check($model, $data)
    {
        $this->addData($model, $data);
        return $this->errorList;
    }
}
