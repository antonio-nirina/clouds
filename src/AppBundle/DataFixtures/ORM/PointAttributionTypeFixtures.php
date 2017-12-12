<?php

namespace AppBundle\DataFixtures\ORM;

use AdminBundle\Entity\PointAttributionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Component\PointAttribution\PointAttributionType as PointAttributionTypeName;

class PointAttributionTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $arr_point_type = [
            ['attribution des points par tranches de CA', 'performance_1'],
            ['attribution des points par tranches de classement', 'performance_2'],
        ];

        foreach ($arr_point_type as $arr) {
            $point_type = new PointAttributionType();
            $point_type->setPointTypeName($arr[1]);
            $point_type->setDescription($arr[0]);

            $manager->persist($point_type);
        }

        $type_product_turnover_propo = new PointAttributionType();
        $type_product_turnover_propo->setPointTypeName(PointAttributionTypeName::PRODUCT_TURNOVER_PROPORTIONAL)
            ->setDescription('attribution des points proportionnelle au CA');
        $manager->persist($type_product_turnover_propo);

        $type_product_turnover_slice = new PointAttributionType();
        $type_product_turnover_slice->setPointTypeName(PointAttributionTypeName::PRODUCT_TURNOVER_SLICE)
            ->setDescription('attribution des points par tranches de CA');
        $manager->persist($type_product_turnover_slice);

        $manager->flush();

        $this->addReference('type-product-turnover-propo', $type_product_turnover_propo);
        $this->addReference('type-product-turnover-slice', $type_product_turnover_slice);
    }
}
