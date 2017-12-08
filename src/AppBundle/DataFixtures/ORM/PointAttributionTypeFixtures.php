<?php

namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\PointAttributionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PointAttributionTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $arr_point_type = [
            ['attribution des points par tranches de CA', 'performance_1'],
            ['attribution des points par tranches de classement', 'performance_2']
        ];

        foreach ($arr_point_type as $arr) {
            $point_type = new PointAttributionType();
            $point_type->setPointTypeName($arr[1]);
            $point_type->setDescription($arr[0]);

            $manager->persist($point_type);
        }

        $manager->flush();
    }
}
