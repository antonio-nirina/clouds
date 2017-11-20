<?php

namespace AdminBundle\Service\ImportExport;

use AdminBundle\Service\FileHandler\CSVFileContentBrowser;
use AdminBundle\Service\FileHandler\CSVHandler;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\ProgramUserCompany;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use AdminBundle\Entity\ProgramUser;
use UserBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;

class RegistrationImporter extends CSVFileContentBrowser
{
    private $site_form_setting;
    private $manager;
    private $user_manager;

    public function __construct(CSVHandler $csv_handler, EntityManager $manager, UserManager $user_manager)
    {
        parent::__construct($csv_handler);
        $this->manager = $manager;
        $this->user_manager = $user_manager;
    }

    public function setSiteFormSetting(SiteFormSetting $site_form_setting)
    {
        $this->site_form_setting = $site_form_setting;
    }

    public function importData($model, $data)
    {
        $this->addData($model, $data);
        $this->increaseRowIndexToNextNotBlankRow(); // go to company data title row, following given structure
        $this->increaseRowIndex(); // go to company data header row, following given structure
        $company_header_row = $this->array_data[$this->row_index];
        $this->increaseRowIndex(); // go to company data row, following given structure
        $program_user_company = $this->createCompanyData($this->array_data[$this->row_index], $company_header_row);
        $this->increaseRowIndex(); // go to blank line
        $this->increaseRowIndexToNextNotBlankRow(); // go to user data title row, following given structure
        $this->increaseRowIndex(); // go to user data header row, following given structure
        $user_header_row = $this->array_data[$this->row_index];
        $this->increaseRowIndex(); // go to first user data, following given structure
        $user_list = $this->createUserData($this->row_index, $user_header_row, $program_user_company);

        $this->manager->persist($program_user_company);
        foreach ($user_list as $user_element) {
            $this->manager->persist($user_element["program_user"]);
            $this->user_manager->updateUser($user_element["app_user"]);
        }
        $this->manager->flush();

        return;
    }

    private function createCompanyData($row, $header_row)
    {
        $program_user_company = new ProgramUserCompany();
        $additional_data = array();
        foreach ($row as $key => $col_element) {
            if ("" != $header_row[$key]) {
                $related_field_setting = $this->manager->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                    ->findBySiteFormSettingAndLabel($this->site_form_setting, $header_row[$key]);
                if (!is_null($related_field_setting)) {
                    switch ($related_field_setting->getSpecialFieldIndex()) {
                        case SpecialFieldIndex::USER_COMPANY_NAME:
                            $program_user_company->setName($col_element);
                            break;
                        case SpecialFieldIndex::USER_COMPANY_POSTAL_ADDRESS:
                            $program_user_company->setPostalAddress($col_element);
                            break;
                        case SpecialFieldIndex::USER_COMPANY_POSTAL_CODE:
                            $program_user_company->setPostalCode($col_element);
                            break;
                        case SpecialFieldIndex::USER_COMPANY_CITY:
                            $program_user_company->setCity($col_element);
                            break;
                        case SpecialFieldIndex::USER_COMPANY_COUNTRY:
                            $program_user_company->set($col_element);
                            break;
                        default:
                            $additional_data[$header_row[$key]] = $col_element;
                    }
                }
            }
        }
        $program_user_company->setCustomization($additional_data);

        return $program_user_company;
    }

    public function createUserData($first_row_index, $header_row, $program_user_company)
    {
        $user_list = array();
        $blank_row = false;
        $this->row_index = $first_row_index;
        while (!$blank_row) {
            $program_user = new ProgramUser();
            $program_user->setProgram($this->site_form_setting->getProgram());
            $app_user = $this->user_manager->createUser();
            $additional_data = array();
            foreach ($this->array_data[$this->row_index] as $key => $col_element) {
                if ("" != $header_row[$key]) {
                    $related_field_setting = $this->manager->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                        ->findBySiteFormSettingAndLabel($this->site_form_setting, $header_row[$key]);
                    if (!is_null($related_field_setting)) {
                        switch ($related_field_setting->getSpecialFieldIndex()) {
                            case SpecialFieldIndex::USER_NAME:
                                $app_user->setName($col_element);
                                break;
                            case SpecialFieldIndex::USER_FIRSTNAME:
                                $app_user->setFirstname($col_element);
                                break;
                            case SpecialFieldIndex::USER_EMAIL:
                                $app_user->setEmail($col_element);
                                break;
                            case SpecialFieldIndex::USER_CIVILITY:
                                $app_user->setCivility($col_element);
                                break;
                            case SpecialFieldIndex::USER_PRO_EMAIL:
                                $app_user->setProEmail($col_element);
                                break;
                            case SpecialFieldIndex::USER_PHONE:
                                $app_user->setPhone($col_element);
                                break;
                            case SpecialFieldIndex::USER_MOBILE_PHONE:
                                $app_user->setMobilePhone($col_element);
                                break;
                            default:
                                $additional_data[$header_row[$key]] = $col_element;
                        }
                    }
                }
            }
            $app_user->setProgramUser($program_user);
            $program_user->setAppUser($app_user);

            $program_user_company->addProgramUser($program_user);
            $program_user->setProgramUserCompany($program_user_company);

            $user_element = array();
            $user_element["app_user"] = $app_user;
            $user_element["program_user"] = $program_user;
            array_push($user_list, $user_element);

            if ($this->increaseRowIndex()) {
                if ($this->csv_handler->isBlankRow($this->array_data[$this->row_index])) {
                    $blank_row = true;
                }
            } else {
                break;
            }
        }

        return $user_list;
    }
}
