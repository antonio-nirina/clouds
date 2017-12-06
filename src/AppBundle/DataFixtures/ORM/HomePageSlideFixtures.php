<?php
namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Form\HomePageEditorialType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\HomePageSlide;
use AdminBundle\Component\Slide\SlideType;

class HomePageSlideFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $slide_1 = new HomePageSlide();
        $slide_1->setSlideOrder(1)
            ->setMessage('CHALLENGE 2017')
            ->setSlideType(SlideType::IMAGE);

        $slide_2 = new HomePageSlide();
        $slide_2->setSlideOrder(2)
            ->setMessage('CHALLENGE 2017')
            ->setSlideType(SlideType::IMAGE);

        $slide_3 = new HomePageSlide();
        $slide_3->setSlideOrder(3)
            ->setMessage('CHALLENGE 2017')
            ->setSlideType(SlideType::IMAGE);

        $slide_4 = new HomePageSlide();
        $slide_4->setSlideOrder(4)
            ->setSlideType(SlideType::VIDEO);

        $manager->persist($slide_1);
        $manager->persist($slide_2);
        $manager->persist($slide_3);
        $manager->persist($slide_4);

        $manager->flush();

        $this->addReference('home-page-slide-1', $slide_1);
        $this->addReference('home-page-slide-2', $slide_2);
        $this->addReference('home-page-slide-3', $slide_3);
        $this->addReference('home-page-slide-4', $slide_4);
    }
}