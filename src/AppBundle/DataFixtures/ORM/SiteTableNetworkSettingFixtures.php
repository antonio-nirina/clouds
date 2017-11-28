<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\SiteTableNetworkSetting;

class SiteTableNetworkSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $table_network = new SiteTableNetworkSetting();
        $table_network->setHasClassment(false);
        $table_network->setHasLike(false);
        $table_network->setHasFacebook(false);
        $table_network->setHasLinkedin(false);
        $table_network->setHasTwitter(false);
        $table_network->setFacebookLink('');
        $table_network->setLinkedinLink('');
        $table_network->setTwitterLink('');
        $table_network->setProgram($this->getReference('program'));

        $manager->persist($table_network);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ProgramFixtures::class,
        );
    }
}
