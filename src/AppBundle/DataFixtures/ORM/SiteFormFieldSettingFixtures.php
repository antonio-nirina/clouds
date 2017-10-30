<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\FieldType;
use AppBundle\DataFixtures\ORM\SiteFormFixtures;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

class SiteFormFieldSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $company = new SiteFormFieldSetting();
        $company->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("société")
            ->setFieldOrder(4)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_COMPANY_NAME)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($company);

        $address = new SiteFormFieldSetting();
        $address->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("adresse postale")
            ->setFieldOrder(6)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_COMPANY_POSTAL_ADDRESS)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($address);

        $postal_code = new SiteFormFieldSetting();
        $postal_code->setFieldType(FieldType::NUM_TEXT)
            ->setMandatory(true)
            ->setLabel("code postale")
            ->setFieldOrder(7)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_COMPANY_POSTAL_CODE)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($postal_code);

        $city = new SiteFormFieldSetting();
        $city->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("ville")
            ->setFieldOrder(8)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_COMPANY_CITY)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($city);

        $phone = new SiteFormFieldSetting();
        $phone->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("numéro de téléphone")
            ->setFieldOrder(12)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_PHONE)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($phone);

        /*$net = new SiteFormFieldSetting();
        $net->setFieldType(FieldType::TEXT)
            ->setMandatory(false)
            ->setLabel("réseau")
            ->setFieldOrder(6)
            ->setPublished(true)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($net);*/

        $civility = new SiteFormFieldSetting();
        $civility->setFieldType(FieldType::CHOICE_RADIO)
            ->setMandatory(true)
            ->setLabel("civilité")
            ->setFieldOrder(1)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_CIVILITY)
            ->setAdditionalData(array(
                "choices" => array(
                    "Mme" => "Mme",
                    "M." => "M.",
                )
            ))
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($civility);

        $firstname = new SiteFormFieldSetting();
        $firstname->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("prénom")
            ->setFieldOrder(2)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_FIRSTNAME)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($firstname);

        $name = new SiteFormFieldSetting();
        $name->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("nom")
            ->setFieldOrder(3)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_NAME)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($name);

        $function = new SiteFormFieldSetting();
        $function->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("fonction")
            ->setFieldOrder(5)
            ->setPublished(true)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($function);

        $email = new SiteFormFieldSetting();
        $email->setFieldType(FieldType::EMAIL)
            ->setMandatory(true)
            ->setLabel("email professionnel")
            ->setFieldOrder(10)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_EMAIL)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($email);

        /*$phone_2 = new SiteFormFieldSetting();
        $phone_2->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("numéro de téléphone")
            ->setFieldOrder(12)
            ->setPublished(true)
            ->setSpecialFieldIndex(SpecialFieldIndex::USER_PHONE)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($phone_2);*/

        $country = new SiteFormFieldSetting();
        $country->setFieldType(FieldType::TEXT)
            ->setLabel("pays")
            ->setFieldOrder(9)
            ->setPublished(true)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($country);


        $manager->persist($company);
        $manager->persist($address);
        $manager->persist($postal_code);
        $manager->persist($city);
        $manager->persist($phone);
//        $manager->persist($net);
        $manager->persist($civility);
        $manager->persist($firstname);
        $manager->persist($name);
        $manager->persist($function);
        $manager->persist($email);
//        $manager->persist($phone_2);
        $manager->persist($country);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            SiteFormFixtures::class,
        );
    }
}