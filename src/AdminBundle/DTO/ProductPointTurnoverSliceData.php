<?php
namespace AdminBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\PointAttributionSetting;

class ProductPointTurnoverSliceData
{
    private $product_point_attribution_settings;
    private $status;

    public function __construct()
    {
        $this->product_point_attribution_settings = new ArrayCollection();
    }

    public function addProductPointAttributionSettings(PointAttributionSetting $setting)
    {
        $this->product_point_attribution_settings[] = $setting;

        return $this;
    }

    public function removeProductPointAttributionSettings(PointAttributionSetting $setting)
    {
        $this->product_point_attribution_settings->removeElement($setting);
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}