<?php
namespace AdminBundle\Manager;

use AdminBundle\Entity\PointAttributionSetting;
use AdminBundle\Entity\Program;
use Doctrine\ORM\EntityManager;
use AdminBundle\Component\PointAttribution\PointAttributionType;
use AdminBundle\Component\PointAttribution\PointAttributionStatus;
use Doctrine\Common\Collections\ArrayCollection;
use AdminBundle\DTO\ProductPointSettingData;
use AdminBundle\DTO\ProductPointSettingUnitData;
use AdminBundle\DTO\ProductPointTurnoverSliceData;
use AdminBundle\Component\PointAttribution\SliceCategory;

class ProductPointAttributionManager
{
    private $em;
    private $program;
    const MAX_PRODUCT_GROUP = '5';

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
                $product_point_turnover_slice_a = $this->em
                    ->getRepository('AdminBundle\Entity\PointAttributionSetting')
                    ->findOneByProgramAndTypeAndProductGroupAndName(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE,
                        $product_group['product_group'],
                        SliceCategory::SLICE_A
                    );
                if (!is_null($product_point_turnover_slice_a)) {
                    $product_point_turnover_slice_data->setProductPointTurnoverSliceA($product_point_turnover_slice_a);
                    $product_point_turnover_slice_data->setStatus($product_point_turnover_slice_a->getStatus());
                }

                $product_point_turnover_slice_b = $this->em
                    ->getRepository('AdminBundle\Entity\PointAttributionSetting')
                    ->findOneByProgramAndTypeAndProductGroupAndName(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE,
                        $product_group['product_group'],
                        SliceCategory::SLICE_B
                    );
                if (!is_null($product_point_turnover_slice_b)) {
                    $product_point_turnover_slice_data->setProductPointTurnoverSliceB($product_point_turnover_slice_b);
                }

                $product_point_turnover_slice_c = $this->em
                    ->getRepository('AdminBundle\Entity\PointAttributionSetting')
                    ->findOneByProgramAndTypeAndProductGroupAndName(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE,
                        $product_group['product_group'],
                        SliceCategory::SLICE_C
                    );
                if (!is_null($product_point_turnover_slice_c)) {
                    $product_point_turnover_slice_data->setProductPointTurnoverSliceC($product_point_turnover_slice_c);
                }

                $product_point_setting_unit_data->setProductPointTurnoverSlice($product_point_turnover_slice_data);
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
            ->findOneBy(array(
                'point_type_name' => $type_name,
            ));
    }

    public function saveProductPointSettingData(ProductPointSettingData $product_point_setting_data, $program)
    {
        foreach ($product_point_setting_data->getProductPointSettingList() as $setting_data) {
            if ($setting_data->getNewStatus()) {
                $product_point_turnover_proportional = $setting_data->getProductPointTurnoverProportional();
                $product_point_turnover_proportional
                    ->setType(
                        $this->retrieveProductAttributionType(
                            $program,
                            PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL
                        )
                    );
                $product_point_turnover_proportional->setProgram($program);
                $product_point_turnover_proportional->setProductGroup($setting_data->getProductGroup());
                $this->em->persist($product_point_turnover_proportional);
                $program->addPointAttributionSetting($product_point_turnover_proportional);

                $product_point_turnover_slice_a = $setting_data->getProductPointTurnoverSlice()
                    ->getProductPointTurnoverSliceA();
                $product_point_turnover_slice_a->setProgram($program);
                $product_point_turnover_slice_a->setProductGroup($setting_data->getProductGroup());
                $product_point_turnover_slice_a->setStatus(
                    $setting_data->getProductPointTurnoverSlice()->getStatus()
                );
                $product_point_turnover_slice_a->setName(SliceCategory::SLICE_A);
                $product_point_turnover_slice_a->setType(
                    $this->retrieveProductAttributionType(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE
                    )
                );
                $this->em->persist($product_point_turnover_slice_a);
                $program->addPointAttributionSetting($product_point_turnover_slice_a);

                $product_point_turnover_slice_b = $setting_data->getProductPointTurnoverSlice()
                    ->getProductPointTurnoverSliceB();
                $product_point_turnover_slice_b->setProgram($program);
                $product_point_turnover_slice_b->setProductGroup($setting_data->getProductGroup());
                $product_point_turnover_slice_b->setStatus(
                    $setting_data->getProductPointTurnoverSlice()->getStatus()
                );
                $product_point_turnover_slice_b->setName(SliceCategory::SLICE_B);
                $product_point_turnover_slice_b->setType(
                    $this->retrieveProductAttributionType(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE
                    )
                );
                $this->em->persist($product_point_turnover_slice_b);
                $program->addPointAttributionSetting($product_point_turnover_slice_b);

                $product_point_turnover_slice_c = $setting_data->getProductPointTurnoverSlice()
                    ->getProductPointTurnoverSliceC();
                $product_point_turnover_slice_c->setProgram($program);
                $product_point_turnover_slice_c->setProductGroup($setting_data->getProductGroup());
                $product_point_turnover_slice_c->setStatus(
                    $setting_data->getProductPointTurnoverSlice()->getStatus()
                );
                $product_point_turnover_slice_c->setName(SliceCategory::SLICE_C);
                $product_point_turnover_slice_c->setType(
                    $this->retrieveProductAttributionType(
                        $program,
                        PointAttributionType::PRODUCT_TURNOVER_SLICE
                    )
                );
                $this->em->persist($product_point_turnover_slice_c);
                $program->addPointAttributionSetting($product_point_turnover_slice_c);
            } else {
                $setting_data->getProductPointTurnoverProportional()->setProductGroup(
                    $setting_data->getProductGroup()
                );

                $setting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceA()->setStatus(
                    $setting_data->getProductPointTurnoverSlice()->getStatus()
                );
                $setting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceA()->setProductGroup(
                    $setting_data->getProductGroup()
                );

                $setting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceB()->setStatus(
                    $setting_data->getProductPointTurnoverSlice()->getStatus()
                );
                $setting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceB()->setProductGroup(
                    $setting_data->getProductGroup()
                );

                $setting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceC()->setStatus(
                    $setting_data->getProductPointTurnoverSlice()->getStatus()
                );
                $setting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceC()->setProductGroup(
                    $setting_data->getProductGroup()
                );
            }
        }

        return;
    }

    public function deleteUselessProductPointSettingData(
        ProductPointSettingData $product_point_setting_data,
        $original_product_setting_datas,
        $program
    ) {
        foreach ($original_product_setting_datas as $original_product_settting_data) {
            if (false
                ===
                $product_point_setting_data->getProductPointSettingList()->contains($original_product_settting_data)
            ) {
                $program->removePointAttributionSetting(
                    $original_product_settting_data->getProductPointTurnoverProportional()
                );
                $program->removePointAttributionSetting(
                    $original_product_settting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceA()
                );
                $program->removePointAttributionSetting(
                    $original_product_settting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceB()
                );
                $program->removePointAttributionSetting(
                    $original_product_settting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceC()
                );

                $this->em->remove($original_product_settting_data->getProductPointTurnoverProportional());
                $this->em->remove(
                    $original_product_settting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceA()
                );
                $this->em->remove(
                    $original_product_settting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceB()
                );
                $this->em->remove(
                    $original_product_settting_data->getProductPointTurnoverSlice()->getProductPointTurnoverSliceC()
                );
            }
        }
    }

    public function newProductGroupPointAttribution($program)
    {
        $max_product_group = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->retrieveMaxProductGroupByProgramAndType($program, PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL);

        if ($max_product_group >= self::MAX_PRODUCT_GROUP) {
            return -1;
        }

        $prod_point_setting_turnover_propo = new PointAttributionSetting();
        $prod_point_setting_turnover_propo->setType(
            $this->retrieveProductAttributionType(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL
            )
        );
        $prod_point_setting_turnover_propo->setProgram($program);
        $prod_point_setting_turnover_propo->setProductGroup($max_product_group + 1);
        $prod_point_setting_turnover_propo->setStatus(PointAttributionStatus::ON);
        $program->addPointAttributionSetting($prod_point_setting_turnover_propo);
        $this->em->persist($prod_point_setting_turnover_propo);

        $prod_point_setting_turnover_slice_A = new PointAttributionSetting();
        $prod_point_setting_turnover_slice_A->setType(
            $this->retrieveProductAttributionType(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_SLICE
            )
        );
        $prod_point_setting_turnover_slice_A->setProgram($program);
        $prod_point_setting_turnover_slice_A->setProductGroup($max_product_group + 1);
        $prod_point_setting_turnover_slice_A->setName(SliceCategory::SLICE_A);
        $prod_point_setting_turnover_slice_A->setStatus(PointAttributionStatus::OFF);
        $this->em->persist($prod_point_setting_turnover_slice_A);

        $prod_point_setting_turnover_slice_B = new PointAttributionSetting();
        $prod_point_setting_turnover_slice_B->setType(
            $this->retrieveProductAttributionType(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_SLICE
            )
        );
        $prod_point_setting_turnover_slice_B->setProgram($program);
        $prod_point_setting_turnover_slice_B->setProductGroup($max_product_group + 1);
        $prod_point_setting_turnover_slice_B->setName(SliceCategory::SLICE_B);
        $prod_point_setting_turnover_slice_B->setStatus(PointAttributionStatus::OFF);
        $this->em->persist($prod_point_setting_turnover_slice_B);

        $prod_point_setting_turnover_slice_C = new PointAttributionSetting();
        $prod_point_setting_turnover_slice_C->setType(
            $this->retrieveProductAttributionType(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_SLICE
            )
        );
        $prod_point_setting_turnover_slice_C->setProgram($program);
        $prod_point_setting_turnover_slice_C->setProductGroup($max_product_group + 1);
        $prod_point_setting_turnover_slice_C->setName(SliceCategory::SLICE_C);
        $prod_point_setting_turnover_slice_C->setStatus(PointAttributionStatus::OFF);
        $this->em->persist($prod_point_setting_turnover_slice_C);

        $this->em->flush();

        return $max_product_group + 1;
    }

    public function deleteProductGroupPointAttribution($product_group, $program)
    {
        $prod_point_setting_turnover_propo = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->findByProgramAndTypeAndProductGroup(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL,
                $product_group
            );
        if (!empty($prod_point_setting_turnover_propo)) {
            $program->removePointAttributionSetting($prod_point_setting_turnover_propo[0]);
            $this->em->remove($prod_point_setting_turnover_propo[0]);
        }

        $prod_point_setting_turnover_slice_A = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
        ->findOneByProgramAndTypeAndProductGroupAndName(
            $program,
            PointAttributionType::PRODUCT_TURNOVER_SLICE,
            $product_group,
            SliceCategory::SLICE_A
        );
        if (!is_null($prod_point_setting_turnover_slice_A)) {
            $program->removePointAttributionSetting($prod_point_setting_turnover_slice_A);
            $this->em->remove($prod_point_setting_turnover_slice_A);
        }

        $prod_point_setting_turnover_slice_B = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->findOneByProgramAndTypeAndProductGroupAndName(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_SLICE,
                $product_group,
                SliceCategory::SLICE_B
            );
        if (!is_null($prod_point_setting_turnover_slice_B)) {
            $program->removePointAttributionSetting($prod_point_setting_turnover_slice_B);
            $this->em->remove($prod_point_setting_turnover_slice_B);
        }

        $prod_point_setting_turnover_slice_C = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->findOneByProgramAndTypeAndProductGroupAndName(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_SLICE,
                $product_group,
                SliceCategory::SLICE_C
            );
        if (!is_null($prod_point_setting_turnover_slice_C)) {
            $program->removePointAttributionSetting($prod_point_setting_turnover_slice_C);
            $this->em->remove($prod_point_setting_turnover_slice_C);
        }

        $this->em->flush();
    }

    public function redefineProductGroup($product_group, $program)
    {
        $prod_point_setting_turnover_propo_list = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->findByProgramAndTypeAndAfterProductGroup(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_PROPORTIONAL,
                $product_group
            );
        if (!empty($prod_point_setting_turnover_propo_list)) {
            foreach ($prod_point_setting_turnover_propo_list as $prod_point_setting_turnover_propo) {
                $prod_point_setting_turnover_propo->setProductGroup(
                    (int)$prod_point_setting_turnover_propo->getProductGroup() - 1
                );
            }
        }

        $prod_point_setting_turnover_slice_list = $this->em->getRepository('AdminBundle\Entity\PointAttributionSetting')
            ->findByProgramAndTypeAndAfterProductGroup(
                $program,
                PointAttributionType::PRODUCT_TURNOVER_SLICE,
                $product_group
            );
        if (!empty($prod_point_setting_turnover_slice_list)) {
            foreach ($prod_point_setting_turnover_slice_list as $prod_point_setting_turnover_slice) {
                $prod_point_setting_turnover_slice->setProductGroup(
                    (int)$prod_point_setting_turnover_slice->getProductGroup() - 1
                );
            }
        }

        $this->em->flush();
    }

    public function flush()
    {
        $this->em->flush();
    }
}