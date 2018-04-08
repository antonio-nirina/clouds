<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\LoginPortalData;
use AppBundle\DataFixtures\ORM\LoginPortalSlideFixtures;

class LoginPortalDataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $login_portal_data = new LoginPortalData();
        $login_portal_data->setTitle('CHALLENGE 2017')
            ->setText(
                '<p style="text-align:center"><strong>Bienvenue sur le challenge 2017 !</strong></p>

<p style="text-align:center">Lorem ipsum dolor sit amet, forensibus eloquentiam mel ad, et eos dolorem comprehensam. Sonet legendos accusamus sed cu, autem debet eos ut. Mea eu prodesset efficiantur, an per illum apeirian ocurreret, mea vide postea nostrud no. Ne justo fastidii has, ea ridens corrumpit accommodare has, tantas ancillae sea in.</p>'
            );

        $login_portal_data->addLoginPortalSlide($this->getReference('login-portal-slide-1'));
        $this->getReference('login-portal-slide-1')->setLoginPortalData($login_portal_data);
        $login_portal_data->addLoginPortalSlide($this->getReference('login-portal-slide-2'));
        $this->getReference('login-portal-slide-2')->setLoginPortalData($login_portal_data);
        $login_portal_data->addLoginPortalSlide($this->getReference('login-portal-slide-3'));
        $this->getReference('login-portal-slide-3')->setLoginPortalData($login_portal_data);

        $manager->persist($login_portal_data);
        $manager->flush();

        $this->addReference('login-portal-data', $login_portal_data);
    }

    public function getDependencies()
    {
        return array(
            LoginPortalSlideFixtures::class,
        );
    }
}