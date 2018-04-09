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
use AdminBundle\Exception\NoSiteFormSettingSetException;
use AdminBundle\Service\EntityHydrator\UserHydrator;
use AdminBundle\Service\EntityHydrator\ProgramUserCompanyHydrator;

class RegistrationImporter extends CSVFileContentBrowser
{
    private $site_form_setting;
    private $manager;
    private $user_manager;
    private $user_hydrator;
    private $program_user_company_hydrator;

    public function __construct(
        CSVHandler $csv_handler,
        EntityManager $manager,
        UserManager $user_manager,
        UserHydrator $user_hydrator,
        ProgramUserCompanyHydrator $program_user_company_hydrator
    ) {
        parent::__construct($csv_handler);
        $this->manager = $manager;
        $this->user_manager = $user_manager;
        $this->user_hydrator = $user_hydrator;
        $this->program_user_company_hydrator = $program_user_company_hydrator;
    }

    public function setSiteFormSetting(SiteFormSetting $site_form_setting)
    {
        $this->site_form_setting = $site_form_setting;
    }

    public function importData($model, $data)
    {
        if (is_null($this->site_form_setting)) {
            throw new NoSiteFormSettingSetException();
        }

        $this->addData($model, $data);
        $this->increaseRowIndexToNextNotBlankRow(); // go to company data title row, following given structure
        $this->increaseRowIndex(); // go to company data header row, following given structure
        $company_header_row = $this->array_data[$this->row_index];
        $this->increaseRowIndex(); // go to company data row, following given structure

        $user_company_name_field = $this->manager
            ->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
            ->findOneBySiteFormSettingAndSpecialIndex($this->site_form_setting, SpecialFieldIndex::USER_COMPANY_NAME);
        $program = $this->site_form_setting->getProgram();

        $program_user_company = null;
        if (!is_null($user_company_name_field)) {
            $user_company_name_index = array_keys(
                $company_header_row,
                $user_company_name_field->getLabel()
            )[0];
            $user_company_name = $this->array_data[$this->row_index][$user_company_name_index];
            $user_company = $this->manager
                ->getRepository('AdminBundle\Entity\ProgramUserCompany')
                ->findOneByNameAndProgram($user_company_name, $program);
            if (is_null($user_company)) {
                $program_user_company = $this->createCompanyData(
                    $this->array_data[$this->row_index],
                    $company_header_row
                );
                $this->manager->persist($program_user_company);
            } else {
                $program_user_company = $user_company;
            }
        } else {
            $program_user_company = $this->createCompanyData(
                $this->array_data[$this->row_index],
                $company_header_row
            );
            $this->manager->persist($program_user_company);
        }

        $this->increaseRowIndex(); // go to blank line
        $this->increaseRowIndexToNextNotBlankRow(); // go to user data title row, following given structure
        $this->increaseRowIndex(); // go to user data header row, following given structure
        $user_header_row = $this->array_data[$this->row_index];
        $this->increaseRowIndex(); // go to first user data, following given structure
        $user_list = $this->createUserData($this->row_index, $user_header_row, $program_user_company);

        foreach ($user_list as $user_element) {
            $program_user_company->addProgramUser($user_element["program_user"]);
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
                    $this->program_user_company_hydrator->hydrate(
                        $related_field_setting,
                        $header_row[$key],
                        $col_element,
                        $program_user_company
                    );
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

            foreach ($this->array_data[$this->row_index] as $key => $col_element) {
                if ("" != $header_row[$key]) {
                    $related_field_setting = $this->manager->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                        ->findBySiteFormSettingAndLabel($this->site_form_setting, $header_row[$key]);
                    if (!is_null($related_field_setting)) {
                        $app_user = $this->user_hydrator->hydrate(
                            $related_field_setting,
                            $header_row[$key],
                            $col_element,
                            $app_user
                        );
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
