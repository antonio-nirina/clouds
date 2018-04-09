<?php
namespace AdminBundle\DTO;

use AdminBundle\DTO\ProductPointTurnoverSliceData;
use AdminBundle\Entity\PointAttributionSetting;
use Symfony\Component\Validator\Constraints as Assert;

class ProductPointSettingUnitData
{
    /**
     * @Assert\Valid()
     */
    private $product_point_turnover_proportional;

    /**
     * @Assert\Valid()
     */
    private $product_point_turnover_slice;

    private $product_group;
    private $new_status;

    public function __construct()
    {
        $this->new_status = true;
    }

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
    public function getProductGroup()
    {
        return $this->product_group;
    }

    public function setProductGroup($product_group)
    {
        $this->product_group = $product_group;

        return $this;
    }

    public function getNewStatus()
    {
        return $this->new_status;
    }

    public function setNewStatus($status)
    {
        $this->new_status = $status;

        return $this;
    }
}
