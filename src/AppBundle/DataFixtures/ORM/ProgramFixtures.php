<?php

namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

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
        $manager->persist($program);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ProgramTypeFixtures::class,
            ClientFixtures::class
        );
    }
}
