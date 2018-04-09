<?php
namespace AdminBundle\Service\EntityHydrator;

use AdminBundle\Entity\SiteFormFieldSetting;
use UserBundle\Entity\User;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

class UserHydrator
{
    public function hydrate(
        SiteFormFieldSetting $related_field_setting,
        $key,
        $value,
        User $app_user
    ) {
        if (in_array(
            SpecialFieldIndex::USER_NAME,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setName($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_FIRSTNAME,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setFirstname($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_EMAIL,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setEmail($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_CIVILITY,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setCivility($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_PRO_EMAIL,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setProEmail($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_PHONE,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setPhone($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_MOBILE_PHONE,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setMobilePhone($value);
        } elseif (in_array(
            SpecialFieldIndex::USER_PASSWORD,
            $related_field_setting->getSpecialFieldIndex()
        )
        ) {
            $app_user->setPlainPassword($value);
        } else {
            $additional_data[$key] = $value;
            if (is_null($app_user->getCustomization())) {
                $app_user->setCustomization(array());
            }
            $app_user->setCustomization(array_merge($app_user->getCustomization(), $additional_data));
        }

        return $app_user;
    }
}
