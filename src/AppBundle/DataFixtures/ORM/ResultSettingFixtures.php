<?php

namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\ResultSetting;
use AppBundle\DataFixtures\ORM\ProgramFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ResultSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $program = $this->getReference('program');

        $result_setting = new ResultSetting();
        $result_setting->setMonthly(true)
            ->setByRank(false)
            ->setByProduct(true)
            ->setProgram($program);

        $manager->persist($result_setting);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ProgramFixtures::class,
        );
    }
}
