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
    private $productPointTurnoverProportional;

    /**
     * @Assert\Valid()
     */
    private $productPointTurnoverSlice;

    private $productGroup;
    private $newStatus;

    /**
     * ProductPointSettingUnitData constructor.
     */
    public function __construct()
    {
        $this->newStatus = true;
    }

    /**
     * @param PointAttributionSetting $pointAttributionSetting
     * @return $this
     */
    public function setProductPointTurnoverProportional(PointAttributionSetting $pointAttributionSetting)
    {
        $this->productPointTurnoverProportional = $pointAttributionSetting;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductPointTurnoverProportional()
    {
        return $this->productPointTurnoverProportional;
    }

    /**
     * @param \AdminBundle\DTO\ProductPointTurnoverSliceData $data
     * @return $this
     */
    public function setProductPointTurnoverSlice(ProductPointTurnoverSliceData $data)
    {
        $this->productPointTurnoverSlice = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductPointTurnoverSlice()
    {
        return $this->productPointTurnoverSlice;
    }

    /**
     * @return mixed
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * @param $productGroup
     * @return $this
     */
    public function setProductGroup($productGroup)
    {
        $this->productGroup = $productGroup;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setNewStatus($status)
    {
        $this->newStatus = $status;

        return $this;
    }
}
