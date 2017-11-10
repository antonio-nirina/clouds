<?php

namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\ProgramType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProgramTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $arr_program_type = [
            ['Challenge','<span class = "FontRec">le challenge</span> est votre programme de récompenses déstinés à vos force de vente qu\'elles soient directs ou indirects ou qu\'il s\'agisse de vos prescripteurs.','images/cloudsRewards/arrondi1.png'],
            ['Fidélisation','<span class = "FontRec">la fidélisation</span> est votre programme de récompenses déstinés à vos clients Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.','images/cloudsRewards/arrondi2.png'],
            ['Parrainage','<span class = "FontRec">le parrainage</span> est votre programme de récompenses déstinés à vos clients Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.','images/cloudsRewards/arrondi3.png']
        ];

        foreach ($arr_program_type as $arr) {
            $program_type = new ProgramType();
            $program_type->setType($arr[0]);
            $program_type->setPresentation($arr[1]);
            $program_type->setImageUrl($arr[2]);
            $manager->persist($program_type);

            if ($arr[0] == "Challenge") {
                $this->addReference('challenge_program', $program_type);
            } elseif ($arr[0] == "Fidélisation") {
                $this->addReference('challenge_fidelisation', $program_type);
            } else {
                $this->addReference('challenge_parrainage', $program_type);
            }
        }

        $manager->flush();
    }
}
