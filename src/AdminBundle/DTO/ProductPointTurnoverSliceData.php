<?php
namespace AdminBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\PointAttributionSetting;

class ProductPointTurnoverSliceData
{
    private $status;
    private $product_point_turnover_slice_a;
    private $product_point_turnover_slice_b;
    private $product_point_turnover_slice_c;

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setProductPointTurnoverSliceA(PointAttributionSetting $setting)
    {
        $this->product_point_turnover_slice_a = $setting;

        return $this;
    }

    public function getProductPointTurnoverSliceA()
    {
        return  $this->product_point_turnover_slice_a;
    }

    public function setProductPointTurnoverSliceB(PointAttributionSetting $setting)
    {
        $this->product_point_turnover_slice_b = $setting;

        return $this;
    }

    public function getProductPointTurnoverSliceB()
    {
        return  $this->product_point_turnover_slice_b;
    }

    public function setProductPointTurnoverSliceC(PointAttributionSetting $setting)
    {
        $this->product_point_turnover_slice_c = $setting;

        return $this;
    }

    public function getProductPointTurnoverSliceC()
    {
        return  $this->product_point_turnover_slice_c;
    }
}