<?php
namespace AdminBundle\Service\EntityHydrator;

use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Entity\ProgramUserCompany;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

class ProgramUserCompanyHydrator
{
    public function hydrate(
        SiteFormFieldSetting $related_field_setting,
        $key,
        $value,
        ProgramUserCompany $program_user_company
    ) {
        if (in_array(
            SpecialFieldIndex::USER_COMPANY_NAME,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $program_user_company->setName($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_COMPANY_POSTAL_ADDRESS,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $program_user_company->setPostalAddress($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_COMPANY_POSTAL_CODE,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $program_user_company->setPostalCode($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_COMPANY_CITY,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $program_user_company->setCity($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_COMPANY_COUNTRY,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $program_user_company->setCountry($value);
        } else {
            $additional_data[$key] = $value;
            if (is_null($program_user_company->getCustomization())) {
                $program_user_company->setCustomization(array());
            }
            $program_user_company->setCustomization(
                array_merge($program_user_company->getCustomization(), $additional_data)
            );
        }

        return $program_user_company;
    }
}
