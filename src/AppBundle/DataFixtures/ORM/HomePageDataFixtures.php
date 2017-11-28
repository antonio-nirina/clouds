<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\HomePageData;
use AppBundle\DataFixtures\ORM\HomePageSlideFixtures;
use AppBundle\DataFixtures\ORM\EditorialFixtures;

class HomePageDataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $home_page_data = new HomePageData();

        $home_page_data->addHomePageSlide($this->getReference('home-page-slide-1'));
        $this->getReference('home-page-slide-1')->setHomePageData($home_page_data);
        $home_page_data->addHomePageSlide($this->getReference('home-page-slide-2'));
        $this->getReference('home-page-slide-2')->setHomePageData($home_page_data);
        $home_page_data->addHomePageSlide($this->getReference('home-page-slide-3'));
        $this->getReference('home-page-slide-3')->setHomePageData($home_page_data);

        $home_page_data->setEditorial($this->getReference('editorial'));
        $this->getReference('editorial')->setHomePageData($home_page_data);

        $manager->persist($home_page_data);
        $manager->flush();

        $this->addReference('home-page-data', $home_page_data);
    }

    public function getDependencies()
    {
        return array(
            HomePageSlideFixtures::class,
            EditorialFixtures::class,
        );
    }
}