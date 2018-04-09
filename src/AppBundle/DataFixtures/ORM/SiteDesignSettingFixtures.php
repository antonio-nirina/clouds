<?php

namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\SiteDesignSetting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SiteDesignSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $design = new SiteDesignSetting();
        $design->setPolice('Lato');
        $design->setColors(
            array(
            'couleur_1' => '#1d61d4',
            'couleur_1_bis' => '#598fea',
            "couleur_2" => '#7682da',
            "couleur_3" => '#505050',
            "couleur_4" => "#505050",
            'couleur_5' => '#807f81',
            'couleur_6' => '#ebeeef'
            )
        );
        $design->setProgram($this->getReference('program'));

        $manager->persist($design);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ProgramFixtures::class,
        );
    }
}
