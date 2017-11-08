<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setSociety('Bocasay');
        $client->setPostalAddress('Antanimora, Tana 101');
        $client->setPoFirstName('Tendry');
        $client->setPoLastName('Bocasay');
        $client->setPoPost('Dévéloppeur');
        $client->setPoEmail('temaneke@bocasay.com');
        $client->setPoPhone('04521644854555');

        $manager->persist($client);

        $manager->flush();
        $this->addReference('client_test', $client);
    }
}
