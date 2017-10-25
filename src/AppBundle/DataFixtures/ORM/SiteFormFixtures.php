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
        $site_form = new SiteForm();
        $site_form->setName("Formulaire d'inscription")
                    ->setFormType(SiteFormType::REGISTRATION_TYPE);

        $site_form_setting = new SiteFormSetting();
        $site_form_setting->setState(true)
            ->setProgram($this->getReference('program'))
            ->setSiteForm($site_form);
        $site_form->setSiteFormSetting($site_form_setting);
        $this->getReference('program')->addSiteFormSetting($site_form_setting);

        $manager->persist($site_form);
        $manager->persist($site_form_setting);
        $manager->flush();

        $this->addReference('registration-form', $site_form);
        $this->addReference('registration-form-setting', $site_form_setting);
    }

    public function getDependencies()
    {
        return array(
            ProgramFixtures::class,
        );
    }
}