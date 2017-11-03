<?php
namespace AdminBundle\Service\ImportExport;

use AdminBundle\Service\FileHandler\CSVFileContentBrowser;
use AdminBundle\Service\FileHandler\CSVHandler;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\ProgramUserCompany;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

class RegistrationImporter extends CSVFileContentBrowser
{
    private $site_form_setting;
    private $manager;

    public function __construct(CSVHandler $csv_handler, EntityManager $manager)
    {
        parent::__construct($csv_handler);
        $this->manager = $manager;
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
        $this->increaseRowIndexToNextNotBlankRow(); // go to user data title row, following given structure
        $this->increaseRowIndex(); // go to user data header row, following given structure
        $this->increaseRowIndex(); // go to first user data, following given structure
        $user_header_row = $this->array_data[$this->row_index];
        $user_list = $this->createUserData($this->array_data[$this->row_index], $company_header_row);

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

    public function createUserData($first_row, $header_row)
    {

    }
}