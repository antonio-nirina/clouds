<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\SitePagesStandardDefault;

class SitePagesStandardDefaultFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
		$ListPageParDefaut = array();
		$ListPageParDefaut[] = 'présentation de la société';
		$ListPageParDefaut[] = 'découvrez le programme';
		$ListPageParDefaut[] = 'actualité des partenaires';
		$ListPageParDefaut[] = 'cadeaux';
		$ListPageParDefaut[] = 'contact';
		$ListPageParDefaut[] = 'mentions légales';
		$ListPageParDefaut[] = 'règlement';
	
		foreach($ListPageParDefaut as $PagesStandardDefault){
			$site_pages_standard_default = new SitePagesStandardDefault();
			$site_pages_standard_default->setPageName($PagesStandardDefault);
			$manager->persist($site_pages_standard_default);
			$manager->flush();
		}
    }
}