<?php
namespace AdminBundle\DTO;

use AdminBundle\DTO\ProductPointTurnoverSliceData;
use AdminBundle\Entity\PointAttributionSetting;

class ProductPointSettingUnitData
{
    private $product_point_turnover_proportional;
    private $product_point_turnover_slice;


    public function setProductPointTurnoverProportional(PointAttributionSetting $point_attribution_setting)
    {
        $this->product_point_turnover_proportional = $point_attribution_setting;

        return $this;
    }

    public function getProductPointTurnoverProportional()
    {
        return $this->product_point_turnover_proportional;
    }

    public function setProductPointTurnoverSlice(ProductPointTurnoverSliceData $data)
    {
        $this->product_point_turnover_slice = $data;

        return $this;
    }

    public function getProductPointTurnoverSlice()
    {
        return $this->product_point_turnover_slice;
    }
}