<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\RegistrationFormData;

class RegistrationFormDataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $registration_form_data = new RegistrationFormData();
        $registration_form_data->setHeaderImage("")
            ->setHeaderMessage("CHALLENGE 2017")
            ->setIntroductionTextTitle("formulaire d'inscription")
            ->setIntroductionTextContent("Bienvenue sur le challenge 2017 !");

        $manager->persist($registration_form_data);
        $manager->flush();

        $this->addReference("regist-form-data", $registration_form_data);
    }
}
