<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\FieldType;
use AppBundle\DataFixtures\ORM\SiteFormFixtures;

class SiteFormFieldSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $company = new SiteFormFieldSetting();
        $company->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("société")
            ->setOrder(1)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($company);

        $address = new SiteFormFieldSetting();
        $address->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("adresse postale")
            ->setOrder(2)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($address);

        $postal_code = new SiteFormFieldSetting();
        $postal_code->setFieldType(FieldType::NUM_TEXT)
            ->setMandatory(true)
            ->setLabel("code postale")
            ->setOrder(3)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($postal_code);

        $city = new SiteFormFieldSetting();
        $city->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("ville")
            ->setOrder(4)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($city);

        $phone = new SiteFormFieldSetting();
        $phone->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("numéro de téléphone")
            ->setOrder(5)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($phone);

        $net = new SiteFormFieldSetting();
        $net->setFieldType(FieldType::TEXT)
            ->setMandatory(false)
            ->setLabel("réseau")
            ->setOrder(6)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($net);

        $civility = new SiteFormFieldSetting();
        $civility->setFieldType(FieldType::CHOICE_RADIO)
            ->setMandatory(true)
            ->setLabel("civilité")
            ->setOrder(7)
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
            ->setOrder(8)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($firstname);

        $name = new SiteFormFieldSetting();
        $name->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("nom")
            ->setOrder(9)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($name);

        $function = new SiteFormFieldSetting();
        $function->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("fonction")
            ->setOrder(10)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($function);

        $email = new SiteFormFieldSetting();
        $email->setFieldType(FieldType::EMAIL)
            ->setMandatory(true)
            ->setLabel("email professionnel")
            ->setOrder(11)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($email);

        $phone_2 = new SiteFormFieldSetting();
        $phone_2->setFieldType(FieldType::TEXT)
            ->setMandatory(true)
            ->setLabel("numéro de téléphone")
            ->setOrder(12)
            ->setSiteFormSetting($this->getReference("registration-form-setting"));
        $this->getReference("registration-form-setting")->addSiteFormFieldSetting($phone_2);


        $manager->persist($company);
        $manager->persist($address);
        $manager->persist($postal_code);
        $manager->persist($city);
        $manager->persist($phone);
        $manager->persist($net);
        $manager->persist($civility);
        $manager->persist($firstname);
        $manager->persist($name);
        $manager->persist($function);
        $manager->persist($email);
        $manager->persist($phone_2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            SiteFormFixtures::class,
        );
    }
}