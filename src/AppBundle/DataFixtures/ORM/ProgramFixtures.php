<?php
namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\DataFixtures\ORM\RegistrationFormDataFixtures;
use AppBundle\DataFixtures\ORM\LoginPortalDataFixtures;
use AppBundle\DataFixtures\ORM\HomePageDataFixtures;
use AppBundle\DataFixtures\ORM\PointAttributionTypeFixtures;

class ProgramFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $program = new Program();

        $program->setName("bocasay_test");
        $program->setType($this->getReference('challenge_program'));
        $program->setUrl('cloud-rewards.peoplestay.com');
        $program->setIsMultiOperation(false);
        $program->setIsShopping(false);
        $program->setSiteOpen(false);
        $program->setStatus(false);
        $program->setClient($this->getReference('client_test'));
        $program->setDateLaunch(new \DateTime());
        $program->setDotationSupport(false);
        $program->setRegistrationFormData($this->getReference('regist-form-data'));
        $program->setLoginPortalData($this->getReference('login-portal-data'));
        $program->setHomePageData($this->getReference('home-page-data'));

        $this->getReference('regist-form-data')->setProgram($program);
        $this->getReference('login-portal-data')->setProgram($program);

        $program->addPointAttributionSetting($this->getReference('product-point-attrib-setting-1'));
        $this->getReference('product-point-attrib-setting-1')->setProgram($program);
        $program->addPointAttributionSetting($this->getReference('product-point-attrib-setting-2-1'));
        $this->getReference('product-point-attrib-setting-2-1')->setProgram($program);
        $program->addPointAttributionSetting($this->getReference('product-point-attrib-setting-2-2'));
        $this->getReference('product-point-attrib-setting-2-2')->setProgram($program);
        $program->addPointAttributionSetting($this->getReference('product-point-attrib-setting-2-3'));
        $this->getReference('product-point-attrib-setting-2-3')->setProgram($program);

        $manager->persist($program);
        $manager->flush();
        
        $this->addReference('program', $program);
    }

    public function getDependencies()
    {
        return array(
            ProgramTypeFixtures::class,
            ClientFixtures::class,
            RegistrationFormDataFixtures::class,
            LoginPortalDataFixtures::class,
            HomePageDataFixtures::class,
            PointAttributionTypeFixtures::class
        );
    }
}
