<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\LoginPortalSlide;

class LoginPortalSlideFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $slide_1 = new LoginPortalSlide();
        $slide_1->setSlideOrder(1);

        $slide_2 = new LoginPortalSlide();
        $slide_2->setSlideOrder(2);

        $slide_3 = new LoginPortalSlide();
        $slide_3->setSlideOrder(3);

        $manager->persist($slide_1);
        $manager->persist($slide_2);
        $manager->persist($slide_3);

        $manager->flush();

        $this->addReference('login-portal-slide-1', $slide_1);
        $this->addReference('login-portal-slide-2', $slide_2);
        $this->addReference('login-portal-slide-3', $slide_3);
    }
}
