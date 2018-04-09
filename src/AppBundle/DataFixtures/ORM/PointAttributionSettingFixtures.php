<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Entity\PointAttributionSetting;
use AppBundle\DataFixtures\ORM\PointAttributionTypeFixtures;
use AdminBundle\Component\PointAttribution\PointAttributionStatus;
use AdminBundle\Component\PointAttribution\SliceCategory;

class PointAttributionSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product_point_setting_1 = new PointAttributionSetting();
        $product_point_setting_1->setType($this->getReference('type-product-turnover-propo'))
            ->setProductGroup(1)
            ->setStatus(PointAttributionStatus::ON);
        $manager->persist($product_point_setting_1);

        $product_point_setting_2_1 = new PointAttributionSetting();
        $product_point_setting_2_1->setType($this->getReference('type-product-turnover-slice'))
            ->setName(SliceCategory::SLICE_A)
            ->setProductGroup(1)
            ->setStatus(PointAttributionStatus::OFF);
        $manager->persist($product_point_setting_2_1);

        $product_point_setting_2_2 = new PointAttributionSetting();
        $product_point_setting_2_2->setType($this->getReference('type-product-turnover-slice'))
            ->setName(SliceCategory::SLICE_B)
            ->setProductGroup(1)
            ->setStatus(PointAttributionStatus::OFF);
        $manager->persist($product_point_setting_2_2);

        $product_point_setting_2_3 = new PointAttributionSetting();
        $product_point_setting_2_3->setType($this->getReference('type-product-turnover-slice'))
            ->setName(SliceCategory::SLICE_C)
            ->setProductGroup(1)
            ->setStatus(PointAttributionStatus::OFF);
        $manager->persist($product_point_setting_2_3);

        $manager->flush();

        $this->addReference('product-point-attrib-setting-1', $product_point_setting_1);
        $this->addReference('product-point-attrib-setting-2-1', $product_point_setting_2_1);
        $this->addReference('product-point-attrib-setting-2-2', $product_point_setting_2_2);
        $this->addReference('product-point-attrib-setting-2-3', $product_point_setting_2_3);
    }

    public function getDependencies()
    {
        return array(
            PointAttributionTypeFixtures::class,
        );
    }
}
