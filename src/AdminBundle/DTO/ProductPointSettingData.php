<?php
namespace AdminBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\DTO\ProductPointSettingUnitData;

class ProductPointSettingData
{
    private $product_point_setting_list;

    public function __construct()
    {
        $this->product_point_setting_list = new ArrayCollection();
    }

    public function getProductPointSettingList()
    {
        return $this->product_point_setting_list;
    }

    public function addProductPointSettingList(ProductPointSettingUnitData $unit)
    {
        $this->product_point_setting_list[] = $unit;

        return $this;
    }

    public function removeProductPointsSettingList(ProductPointSettingUnitData $unit)
    {
        $this->product_point_setting_list->removeElement($unit);
    }
}