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
        $registration_form_data->setHeaderImage("header_image.png")
            ->setHeaderMessage("CHALLENGE 2017")
            ->setIntroductionTextTitle("Introduction text")
            ->setIntroductionTextContent("This is the content");

        $manager->persist($registration_form_data);
        $manager->flush();

        $this->addReference("regist-form-data", $registration_form_data);
    }
}