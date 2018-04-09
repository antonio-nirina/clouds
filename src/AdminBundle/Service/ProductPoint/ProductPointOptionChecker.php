<?php
namespace AdminBundle\Service\ProductPoint;

use AdminBundle\DTO\ProductPointSettingData;
use AdminBundle\Component\PointAttribution\PointAttributionStatus;

class ProductPointOptionChecker
{
    private $error_list;

    const WARNING_ONE_OPTION_ONLY = 'Une option uniquement';

    public function __construct()
    {
        $this->error_list = array();
    }

    public function check(ProductPointSettingData $product_point_setting_data)
    {
        foreach ($product_point_setting_data->getProductPointSettingList() as $setting_data) {
            $product_point_turnover_proportional = $setting_data->getProductPointTurnoverProportional();
            $product_point_turnover_slice = $setting_data->getProductPointTurnoverSlice();
            if (PointAttributionStatus::ON == $product_point_turnover_proportional->getStatus()
                && PointAttributionStatus::ON == $product_point_turnover_slice->getStatus()
            ) {
                $this->error_list['product-' . $setting_data->getProductGroup()] = self::WARNING_ONE_OPTION_ONLY;
            }
        }

        return $this->error_list;
    }
}
