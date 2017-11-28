<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\Editorial;

class EditorialFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $edito =  new Editorial();
        $manager->persist($edito);
        $manager->flush();

        $this->addReference('editorial', $edito);
    }
}