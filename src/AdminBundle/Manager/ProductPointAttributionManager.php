<?php
namespace AdminBundle\Manager;

use AdminBundle\Entity\PointAttributionSetting;
use AdminBundle\Entity\Program;
use Doctrine\ORM\EntityManager;
use AdminBundle\Component\PointAttribution\PointAttributionType;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\DTO\ProductPointSettingData;
use AdminBundle\DTO\ProductPointSettingUnitData;
use AdminBundle\DTO\ProductPointTurnoverSliceData;

class ProductPointAttributionManager
{
    private $em;
    private $program;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createProductPointSettingData($program)
    {
        $product_point_setting_data = new ProductPointSettingData();

        $product_group_list = $this->retrieveProductGroupList($program);
        if (!empty($product_group_list)) {
            foreach ($product_group_list as $product_group) {
                $product_point_setting_unit_data = new ProductPointSettingUnitData();
                $product_point_setting_unit_data->setNewStatus(false);
                $product_point_turnover_proportional = $this->em
                    ->getRepository('AdminBundle\Entity\PointAttributionSetting')
                    ->findByProgramAndTypeAndProductGroup(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL,
                        $product_group['product_group']
                    );

                if (!empty($product_point_turnover_proportional)) {
                    $product_point_setting_unit_data
                        ->setProductPointTurnoverProportional($product_point_turnover_proportional[0]);
                    $product_point_setting_unit_data->setProductGroup(
                        $product_point_turnover_proportional[0]->getProductGroup()
                    );
                }

                $product_point_turnover_slice_data = new ProductPointTurnoverSliceData();
                $product_point_turnover_slices = $this->em
                    ->getRepository('AdminBundle\Entity\PointAttributionSetting')
                    ->findByProgramAndTypeAndProductGroup(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE,
                        $product_group['product_group']
                    );

                if (!empty($product_point_turnover_slices)) {
                    foreach ($product_point_turnover_slices as $slice) {
                        $product_point_turnover_slice_data->addProductPointAttributionSettings($slice);
                    }
                    $product_point_turnover_slice_data->setStatus($product_point_turnover_slices[0]->getStatus());
                    $product_point_setting_unit_data->setProductPointTurnoverSlice($product_point_turnover_slice_data);
                }

                $product_point_setting_data->addProductPointSettingList($product_point_setting_unit_data);
            }
        }

        return $product_point_setting_data;
    }

    public function retrieveProductGroupList($program)
    {
        $product_group_list = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->retrieveAvailableProductGroupByProgramAndType(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL
            );
        return $product_group_list;
    }

    public function retrieveProductAttributionType($program, $type_name)
    {
        return $this->em->getRepository('AdminBundle\Entity\PointAttributionType')
            ->findOneByPointTypeName($type_name);
    }

    public function saveProductPointSettingData(ProductPointSettingData $product_point_setting_data, $program)
    {
        foreach ($product_point_setting_data->getProductPointSettingList() as $setting_data) {
            if ($setting_data->getNewStatus()) {
                $product_point_turnover_proportional = $setting_data->setProductPointTurnoverProportional();
                $product_point_turnover_proportional
                    ->setType(
                        $this->retrieveProductAttributionType(
                            $program,
                            PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL
                        )
                    );
                $product_point_turnover_proportional->setProgram($program);
                $program->addPointAttributionSetting($product_point_turnover_proportional);
                $product_point_turnover_proportional->setProductGroup($setting_data->getProductGroup());
                $this->em->persist($product_point_turnover_proportional);

                foreach ($setting_data
                             ->getProductPointTurnoverSlice()
                             ->getProductPointAttributionSettings() as $product_point_turnover_slice) {
                    $product_point_turnover_slice->setType(
                        $this->retrieveProductAttributionType(
                            $program,
                            PointAttributionType::PRODUCT_TURNOVER_SLICE
                        )
                    );
                    $product_point_turnover_slice->setProgram($program);
                    $program->addPointAttributionSetting($product_point_turnover_slice);
                    $product_point_turnover_slice->setProductGroup($setting_data->getProductGroup());
                    $product_point_turnover_slice->setStatus($setting_data->getProductPointTurnoverSlice()
                        ->getStatus());
                    $this->em->persist($product_point_turnover_slice);
                }
            } else {
                foreach ($setting_data
                             ->getProductPointTurnoverSlice()
                             ->getProductPointAttributionSettings() as $product_point_turnover_slice) {
                    $product_point_turnover_slice->setStatus($setting_data
                        ->getProductPointTurnoverSlice()->getStatus());
                }
            }


            $this->em->flush();
        }

        return;
    }
}