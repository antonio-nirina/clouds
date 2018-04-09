<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\SiteForm;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Component\SiteForm\SiteFormType;
use AppBundle\DataFixtures\ORM\ProgramFixtures;

class SiteFormFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $all_forms = [
            ["Formulaire d'inscription",SiteFormType::REGISTRATION_TYPE,5],
            ["Formulaire de déclaration produits",SiteFormType::PRODUCT_DECLARATION_TYPE,3],
            ["Formulaire de déclaration leads",SiteFormType::LEAD_DECLARATION_TYPE,3]
        ];

        foreach ($all_forms as $form) {
            $site_form = new SiteForm();
            $site_form->setName($form[0])
                ->setFormType($form[1]);
            $site_form_setting = new SiteFormSetting();
            $site_form_setting->setState(true)
                ->setProgram($this->getReference('program'))
                ->setSiteForm($site_form)
                ->setCustomFieldAllowed($form[2]);
            $site_form->setSiteFormSetting($site_form_setting);
            $this->getReference('program')->addSiteFormSetting($site_form_setting);

            if ($form[1]== SiteFormType::REGISTRATION_TYPE) {
                $this->addReference('registration-form-setting', $site_form_setting);
            } elseif ($form[1]== SiteFormType::PRODUCT_DECLARATION_TYPE) {
                $this->addReference('declaration-product-form-setting', $site_form_setting);
            }

            $manager->persist($site_form);
            $manager->persist($site_form_setting);
        }

        $manager->flush();

        $this->addReference('registration-form', $site_form);
    }

    public function getDependencies()
    {
        return array(
            ProgramFixtures::class,
        );
    }
}
