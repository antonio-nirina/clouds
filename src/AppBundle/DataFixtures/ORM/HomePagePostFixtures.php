<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\HomePagePost;
use AdminBundle\Component\Post\PostType;

class HomePagePostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $parameter_edito =  new HomePagePost();
        $parameter_edito->setTitle('édito')
            ->setPostType(PostType::PARAMETER_EDITO);

        $manager->persist($parameter_edito);
        $manager->flush();

        $this->addReference('parameter-edito', $parameter_edito);
    }
}
