<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $super_admin_user = new User();
        $super_admin_user->setUsername('super_admin')
            ->setPlainPassword('p@ssWo3d')
            ->setEmail('sauser.customm@gmail.com')
            ->setRoles(array('ROLE_SUPER_ADMIN', 'ROLE_ADMIN'))
            ->setEnabled(true);
        $manager->persist($super_admin_user);
        $manager->flush();
    }
}