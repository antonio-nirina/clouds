<?php
namespace AdminBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\DTO\ProductPointSettingUnitData;
use Symfony\Component\Validator\Constraints as Assert;

class ProductPointSettingData
{
    /**
     * @Assert\Valid()
     */
    private $productPointSettingList;

    public function __construct()
    {
        $this->productPointSettingList = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getProductPointSettingList()
    {
        return $this->productPointSettingList;
    }

    /**
     * @param \AdminBundle\DTO\ProductPointSettingUnitData $unit
     * @return $this
     */
    public function addProductPointSettingList(ProductPointSettingUnitData $unit)
    {
        $this->productPointSettingList[] = $unit;

        return $this;
    }

    /**
     * @param \AdminBundle\DTO\ProductPointSettingUnitData $unit
     */
    public function removeProductPointsSettingList(ProductPointSettingUnitData $unit)
    {
        $this->productPointSettingList->removeElement($unit);
    }
}
