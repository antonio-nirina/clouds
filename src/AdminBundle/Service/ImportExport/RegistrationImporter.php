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
    private $siteFormSetting;
    private $manager;
    private $userManager;
    private $userHydrator;
    private $programUserCompanyHydrator;

    public function __construct(
        CSVHandler $csvHandler,
        EntityManager $manager,
        UserManager $userManager,
        UserHydrator $userHydrator,
        ProgramUserCompanyHydrator $programUserCompanyHydrator
    ) {
        parent::__construct($csvHandler);
        $this->manager = $manager;
        $this->userManager = $userManager;
        $this->userHydrator = $userHydrator;
        $this->programUserCompanyHydrator = $programUserCompanyHydrator;
    }

    public function setSiteFormSetting(SiteFormSetting $siteFormSetting)
    {
        $this->siteFormSetting = $siteFormSetting;
    }

    public function importData($model, $data)
    {
        if (is_null($this->siteFormSetting)) {
            throw new NoSiteFormSettingSetException();
        }

        $this->addData($model, $data);
        $this->increaseRowIndexToNextNotBlankRow(); // go to company data title row, following given structure
        $this->increaseRowIndex(); // go to company data header row, following given structure
        $companyHeaderRow = $this->arrayData[$this->rowIndex];
        $this->increaseRowIndex(); // go to company data row, following given structure

        $userCompanyNameField = $this->manager
            ->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
            ->findOneBySiteFormSettingAndSpecialIndex($this->siteFormSetting, SpecialFieldIndex::USER_COMPANY_NAME);
        $program = $this->siteFormSetting->getProgram();

        $programUserCompany = null;
        if (!is_null($userCompanyNameField)) {
            $userCompanyNameIndex = array_keys(
                $companyHeaderRow,
                $userCompanyNameField->getLabel()
            )[0];
            $userCompanyName = $this->arrayData[$this->rowIndex][$userCompanyNameIndex];
            $userCompany = $this->manager
                ->getRepository('AdminBundle\Entity\ProgramUserCompany')
                ->findOneByNameAndProgram($userCompanyName, $program);
            if (is_null($userCompany)) {
                $programUserCompany = $this->createCompanyData(
                    $this->arrayData[$this->rowIndex],
                    $companyHeaderRow
                );
                $this->manager->persist($programUserCompany);
            } else {
                $programUserCompany = $userCompany;
            }
        } else {
            $programUserCompany = $this->createCompanyData(
                $this->arrayData[$this->rowIndex],
                $companyHeaderRow
            );
            $this->manager->persist($programUserCompany);
        }

        $this->increaseRowIndex(); // go to blank line
        $this->increaseRowIndexToNextNotBlankRow(); // go to user data title row, following given structure
        $this->increaseRowIndex(); // go to user data header row, following given structure
        $user_header_row = $this->arrayData[$this->rowIndex];
        $this->increaseRowIndex(); // go to first user data, following given structure
        $userList = $this->createUserData($this->rowIndex, $user_header_row, $programUserCompany);

        foreach ($userList as $userElement) {
            $programUserCompany->addProgramUser($userElement["program_user"]);
            $this->manager->persist($userElement["program_user"]);
            $this->userManager->updateUser($userElement["app_user"]);
        }
        $this->manager->flush();

        return;
    }

    private function createCompanyData($row, $header_row)
    {
        $programUserCompany = new ProgramUserCompany();
        $additional_data = array();
        foreach ($row as $key => $col_element) {
            if ("" != $header_row[$key]) {
                $related_field_setting = $this->manager->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                    ->findBySiteFormSettingAndLabel($this->siteFormSetting, $header_row[$key]);
                if (!is_null($related_field_setting)) {
                    $this->programUserCompanyHydrator->hydrate(
                        $related_field_setting,
                        $header_row[$key],
                        $col_element,
                        $programUserCompany
                    );
                }
            }
        }
        $programUserCompany->setCustomization($additional_data);

        return $programUserCompany;
    }

    public function createUserData($first_row_index, $header_row, $programUserCompany)
    {
        $userList = array();
        $blank_row = false;
        $this->rowIndex = $first_row_index;
        while (!$blank_row) {
            $programUser = new ProgramUser();
            $programUser->setProgram($this->siteFormSetting->getProgram());
            $appUser = $this->userManager->createUser();

            foreach ($this->arrayData[$this->rowIndex] as $key => $col_element) {
                if ("" != $header_row[$key]) {
                    $related_field_setting = $this->manager->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                        ->findBySiteFormSettingAndLabel($this->siteFormSetting, $header_row[$key]);
                    if (!is_null($related_field_setting)) {
                        $appUser = $this->userHydrator->hydrate(
                            $related_field_setting,
                            $header_row[$key],
                            $col_element,
                            $appUser
                        );
                    }
                }
            }

            $appUser->setProgramUser($programUser);
            $programUser->setAppUser($appUser);

            $programUserCompany->addProgramUser($programUser);
            $programUser->setProgramUserCompany($programUserCompany);

            $userElement = array();
            $userElement["app_user"] = $appUser;
            $userElement["program_user"] = $programUser;
            array_push($userList, $userElement);

            if ($this->increaseRowIndex()) {
                if ($this->csvHandler->isBlankRow($this->arrayData[$this->rowIndex])) {
                    $blank_row = true;
                }
            } else {
                break;
            }
        }

        return $userList;
    }
}
