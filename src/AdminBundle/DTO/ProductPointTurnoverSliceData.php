<?php
namespace AdminBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\Entity\PointAttributionSetting;
use Symfony\Component\Validator\Constraints as Assert;

class ProductPointTurnoverSliceData
{
    private $status;

    /**
     * @Assert\Valid()
     */
    private $productPointTurnoverSliceA;

    /**
     * @Assert\Valid()
     */
    private $productPointTurnoverSliceB; 

    /**
     * @Assert\Valid()
     */
    private $productPointTurnoverSliceC;

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param PointAttributionSetting $setting
     * @return $this
     */
    public function setProductPointTurnoverSliceA(PointAttributionSetting $setting)
    {
        $this->productPointTurnoverSliceA = $setting;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductPointTurnoverSliceA()
    {
        return  $this->productPointTurnoverSliceA;
    }

    /**
     * @param PointAttributionSetting $setting
     * @return $this
     */
    public function setProductPointTurnoverSliceB(PointAttributionSetting $setting)
    {
        $this->productPointTurnoverSliceB = $setting;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductPointTurnoverSliceB()
    {
        return  $this->productPointTurnoverSliceB;
    }

    /**
     * @param PointAttributionSetting $setting
     * @return $this
     */
    public function setProductPointTurnoverSliceC(PointAttributionSetting $setting)
    {
        $this->productPointTurnoverSliceC = $setting;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductPointTurnoverSliceC()
    {
        return  $this->productPointTurnoverSliceC;
    }
}
