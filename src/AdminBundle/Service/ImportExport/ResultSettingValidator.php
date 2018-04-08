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
    protected $error_list;
    protected $site_form_setting;
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


    public function __construct(
        CSVHandler $csv_handler,
        EntityManager $manager,
        Container $container,
        ValidatorInterface $validator
    ) {
        parent::__construct($csv_handler);

        $this->csv_handler = $csv_handler;
        $this->manager = $manager;
        $this->error_list = array();
        $this->validator = $validator;
        $this->container = $container;
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
        $message = $error_message. ', Ligne: '.($row_index+1); // 0-based index to 1-based index (human readable)
        return is_null($col_index)
            ? $message
            : $message.', Colonne: '.($col_index+1); // 0-based index to 1-based index (human readable)
    }

    protected function createErrorWithColumn($error_message, $row_index, $col_index)
    {
        $message = $error_message. ', Ligne: '.($row_index+1); // 0-based index to 1-based index (human readable)
        return is_null($col_index)
            ? $message
            : $message.', Colonne: "'.($col_index).'"'; // 0-based index to 1-based index (human readable)
    }

    protected function checkRowResult($header, $array_data, $array_model, $header_row_index, $row_index, $program)
    {
        $i = $row_index;
        $current_row = array_combine($header, $array_data[$i]);
        
        //date check
        $prod_empty = true;
        foreach ($current_row as $index => $col) {
            //impérative
            if (in_array($index, array("Nom", "Prénom", "Fonction"/*,"Rang","Réseau"*/))) {
                if (empty($col)) {
                    $this->addError(
                        $this->createErrorWithColumn(
                            SchemaChecker::ERROR_MISSING_VALUE_ON_MANDATORY_FIELD,
                            $row_index,
                            $index
                        )
                    );
                }
            }
            if ($prod_empty) {//TEST CHIFFRE D'AFFAIRE VIDE
                for ($j=1; $j < 5; $j++) {
                    if ($index == "Produit $j") {
                        if (empty($col)) {
                            $prod_empty = true;
                        } else {
                            $prod_empty = false;
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
                    $current_row[$index],
                    new Regex(array("pattern" => $reg_exp)),
                    $i,
                    $index,
                    self::ERROR_WRONG_DATE_FOMAT
                );
                
                // preg_match($reg_exp, $current_row[$index], $date);
                // if (empty($date)) {
                //     $this->addError(
                //         $this->createErrorWithColumn(
                //             self::ERROR_WRONG_DATE_FOMAT,
                //             $i,
                //             $index
                //         )
                //     );
                // }
            } elseif (!empty($current_row[$index])
                && (                in_array($index, array('Rang')))
            ) { //check integer
                $this->validateColumnElement2(
                    $current_row[$index],
                    new Regex(array("pattern" => "#(^[1-6]$)#")),
                    $i,
                    $index,
                    self::ERROR_WRONG_INTEGER_FORMAT
                );
                // preg_match("#(^[1-6]$)#", $current_row[$index], $num);
                // if (empty($num)) {
                //     $this->addError(
                //         $this->createErrorWithColumn(
                //             self::ERROR_WRONG_INTEGER_FORMAT,
                //             $i,
                //             $index
                //         )
                //     );
                // }
            } elseif (!empty($current_row[$index])
                && (                strpos($index, 'Produit') !== false)
            ) { //check numeric
                $this->validateColumnElement2(
                    $current_row[$index],
                    new Type(array("type" => "numeric")),
                    $i,
                    $index,
                    self::ERROR_WRONG_NUM_FORMAT
                );
            } elseif (!empty($current_row[$index])) { //alphanumeric
                $this->validateColumnElement2(
                    $current_row[$index],
                    new Type(array("type" => "string")),
                    $i,
                    $index,
                    self::ERROR_WRONG_ALPHANUM_FORMAT
                );
            }
        }

        if ($prod_empty) {
            $this->addError(
                $this->createErrorWithIndex(
                    self::ERROR_MISSING_VALUE_ON_PRODUCT_FIELD,
                    $i
                )
            );
        }
        //user check
        $user = $this->manager
            ->getRepository('AdminBundle\Entity\ProgramUser')
            ->findByNameAndLastName(
                $current_row['Nom'],
                $current_row['Prénom'],
                $program
            );
        if (empty($user)) {
            $this->addError(
                $this->createErrorWithIndex(
                    self::ERROR_USER_NOT_FOUND,
                    $i
                )
            );
        }

        return $this->error_list;
    }

    protected function importRowResult($header, $array_data, $array_model, $header_row_index, $row_index, $program)
    {
        $i = $row_index;
        $current_row = array_combine($header, $array_data[$i]);

        //user check
        $program_user = $this->manager
            ->getRepository('AdminBundle\Entity\ProgramUser')
            ->findByNameAndLastName(
                $current_row['Nom'],
                $current_row['Prénom'],
                $program
            );
        $program_user = $program_user[0];

        if (array_key_exists("Fonction", $current_row)) { //assignation role commercial
            $role = $this->manager
                ->getRepository('AdminBundle\Entity\Role')
                ->findBy(
                    array(
                                'name' => $current_row['Fonction'],
                                'program' => $program
                            )
                );

            if (!empty($role)) {
                $role = $role[0];
            } else {
                $role = new Role();
                $role->setName($current_row['Fonction']);
                if ($current_row['Rang']) {
                    $role->setRank($current_row['Rang']);
                }
                // $role->setNetwork($current_row['Réseau']);
                $role->setProgram($program);
                $this->manager->persist($role);
                $this->manager->flush();
            }

            $program_user->setRole($role);
        }

        $sales_point_attribution = $this->container->get('AdminBundle\Service\PointAttribution\SalesPointAttribution');
        for ($i=1; $i < 5; $i++) { //insertion des ventes
            if (array_key_exists("Produit $i", $current_row) && !empty($current_row["Produit $i"])) {
                $sales = new Sales();
                $sales->setCa($current_row["Produit $i"]);
                $sales->setProgramUser($program_user);
                $sales->setProductGroup($i);

                if (array_key_exists("Dénomination $i", $current_row)) {
                    $sales->setProductName($current_row["Dénomination $i"]);
                } else {
                    $sales->setProductName("Produit $i");
                }

                $format = 'd/m/Y H:i:s';
                if (array_key_exists("Date", $current_row)) {
                    $sales->setDate(\DateTime::createFromFormat($format, $current_row["Date"]." 00:00:00"));
                }

                if (array_key_exists("Période de", $current_row)) {
                    $sales->setDateFrom(\DateTime::createFromFormat($format, $current_row["Période de"]." 00:00:00"));
                    $sales->setDateTo(\DateTime::createFromFormat($format, $current_row["à"]." 23:59:59"));
                }

                $this->manager->persist($sales);
                $this->manager->flush();

                /* définition des points pour mise à jour des points */
                $sales_point_attribution->attributedByProduct($sales); //by product and by period
                $sales_point_attribution->updateUserClassmentPerformance($sales); //update performance
                if (array_key_exists("Fonction", $current_row)) {//by rank
                    $sales_point_attribution->attributedByRank($sales);
                }
            }
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

    protected function validateColumnElement2(
        $col_element,
        $type,
        $row_index,
        $col_index,
        $error_if_not_valid = self::ERROR_INVALID_DATA_FORMAT
    ) {
        $violations = $this->validator->validate($col_element, $type);
        if (0 !== count($violations)) {
            $this->addError(
                $this->createErrorWithColumn(
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
