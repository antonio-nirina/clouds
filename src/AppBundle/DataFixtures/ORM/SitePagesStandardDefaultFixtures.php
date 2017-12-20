<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\SitePagesStandardDefault;

class SitePagesStandardDefaultFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
		$ListPageParDefaut = array(
			array(
				'page' => 'présentation de la société',
				'options' => array()
			),
			array(
				'page' => 'découvrez le programme',
				'options' => array()
			),
			array(
				'page' => 'actualité des partenaires',
				'options' => array()
			),
			array(
				'page' => 'cadeaux',
				'options' => array()
			),
			array(
				'page' => 'contact',
				'options' => array(
					array(
						'type' => 'input-text',
						'publier' => '1',
						'obligatoire' => '1',
						'label' => 'prénom',
						'ordre' => '1'
					),
					array(
						'type' => 'input-text',
						'publier' => '1',
						'obligatoire' => '1',
						'label' => 'nom',
						'ordre' => '2'
					),
					array(
						'type' => 'input-text',
						'publier' => '1',
						'obligatoire' => '1',
						'label' => 'e-mail',
						'ordre' => '3'
					),
					array(
						'type' => 'input-textarea',
						'publier' => '1',
						'obligatoire' => '1',
						'label' => 'message',
						'ordre' => '4'
					)
				)
			),
			array(
				'page' => 'mentions légales',
				'options' => array()
			),
			array(
				'page' => 'règlement',
				'options' => array()
			)
		);
	
		foreach($ListPageParDefaut as $PagesStandardDefault){
			$site_pages_standard_default = new SitePagesStandardDefault();
			$site_pages_standard_default->setPageName($PagesStandardDefault['page']);
			$site_pages_standard_default->setOptions($PagesStandardDefault['options']);
			$manager->persist($site_pages_standard_default);
			$manager->flush();
		}
    }
}