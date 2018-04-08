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
        
        //User tests
        $ListUserTest = array(
        array(
        'nom' => 'ANDRIAMBAHINY',
        'prenom' => 'Antsa',
        'email' => 'aandrantsa@bocasay.com',
        'role' => 'ROLE_MANAGER',
        'password' => 'antsa',
        ),
        array(
        'nom' => 'ANDRIAMBAHINY',
        'prenom' => 'Arivolala',
        'email' => 'aarivolala@bocasay.com',
        'role' => 'ROLE_COMMERCIAL',
        'password' => 'lala',
        ),
        array(
        'nom' => 'DUPOND',
        'prenom' => 'Julien',
        'email' => 'ajulien@test.com',
        'role' => 'ROLE_COMMERCIAL',
        'password' => 'julien',
        ),
        array(
        'nom' => 'DURAND',
        'prenom' => 'Julien',
        'email' => 'jdurand@hot.fr',
        'role' => 'ROLE_PARTICIPANT',
        'password' => 'julien',
        ),
        array(
        'nom' => 'PETIT',
        'prenom' => 'Sofia',
        'email' => 'psofia@test.com',
        'role' => 'ROLE_PARTICIPANT',
        'password' => 'sofia',
        ),
        array(
        'nom' => 'RUBEN',
        'prenom' => 'David',
        'email' => 'david@test.com',
        'role' => 'ROLE_PARTICIPANT',
        'password' => 'sofia',
        ),
        array(
        'nom' => 'TOTO',
        'prenom' => 'Foo',
        'email' => 'tfoo@test.com',
        'role' => 'ROLE_COMMERCIAL',
        'password' => 'foo',
        )
        );
        
        foreach($ListUserTest as $UserTest){
            $test_user = new User();
            $test_user->setUsername($UserTest['email'])
                ->setPlainPassword($UserTest['password'])
                ->setEmail($UserTest['email'])
                ->setRoles(array($UserTest['role']))
                ->setName($UserTest['nom'])
                ->setFirstname($UserTest['prenom'])
                ->setEnabled(true);
            $manager->persist($test_user);
            $manager->flush();
        }
    }
}