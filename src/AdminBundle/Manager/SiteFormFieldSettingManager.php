<?php

namespace AdminBundle\Manager;

use AdminBundle\Entity\Program;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Entity\SiteFormSetting;
use Doctrine\ORM\EntityManager;

class SiteFormFieldSettingManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getRepository()
    {
        return $this->em->getRepository('AdminBundle:SiteFormFieldSetting');
    }

    /**
     * [adjustFieldOrder adjust current field]
     * @param  [json] $field_order
     * @param  [json] $current_fields
     */
    public function adjustFieldAndOrder($field_order, $current_fields)
    {
        $order = json_decode($field_order);
        if (is_array($order[0])) {
            $field_order = [];
            foreach ($order as $fo) {
                $order = array_flip($fo);
                $field_order = $field_order + $order;
            }
        } else {
            $field_order = array_flip($order);
        }

        $current_field_list = json_decode($current_fields);
        if (!is_null($current_field_list)) {
            foreach ($current_field_list as $field_data) {
                $field = $this->getRepository()->findOneById(intval($field_data->id));
                if (!is_null($field)) {
                    $field->setPublished(boolval($field_data->published));
                    $field->setMandatory(boolval($field_data->mandatory));
                    if (array_key_exists($field->getId(), $field_order)) {
                        $field->setFieldOrder($field_order[$field->getId()]);
                    }
                }
            }
        }
    }

    /**
     * [addNewFields add new fields]
     * @param json $new_field_list
     * @param SiteFormSetting $site_form_setting
     */
    public function addNewFields($new_field_list, SiteFormSetting $site_form_setting, $no_adjust_allowed = false)
    {
        $new_field_list = json_decode($new_field_list);
        if (!is_null($new_field_list)) {
            foreach ($new_field_list as $new_field) {
                if (!is_null($site_form_setting)
                    &&
                    (
                        is_int($site_form_setting->getCustomFieldAllowed())
                        && $site_form_setting->getCustomFieldAllowed() > 0
                    )
                ) {
                    $field = new SiteFormFieldSetting();
                    $field->setSiteFormSetting($site_form_setting)
                            ->setFieldType($new_field->field_type)
                            ->setMandatory(boolval($new_field->mandatory))
                            ->setLabel($new_field->label)
                            ->setFieldOrder(30); // big value, to put new field at the bottom
                    if (isset($new_field->level)) {
                        $field->setLevel($new_field->level);
                    }

                    if (array_key_exists('choices', $new_field)) {
                        $choices = array_map('strval', (array)$new_field->choices);
                        $choices = array_map('strval', array_flip($choices)); // VALUE is the same as KEY
                        $add_data["choices"] = $choices;
                        $field->setAdditionalData($add_data);
                    }
                    $site_form_setting->addSiteFormFieldSetting($field);
                    $this->em->persist($field);

                    if (!$no_adjust_allowed) {
                        $site_form_setting->setCustomFieldAllowed(($site_form_setting->getCustomFieldAllowed()) - 1);
                    }
                }
            }
        }
    }

    public function updateField(SiteFormFieldSetting $field, $type, $label)
    {
        $type = ($type == 'alphanum')?'text':$type;
        $field->setLabel($label);

        $old_type = $field->getFieldType();
        if ($field->getInRow()) {
            $form_setting_id = $field->getSiteFormSetting()->getId();
            $in_row = $this->getRepository()->findAllInRow($field->getInRow(), $form_setting_id, $field->getLevel());

            foreach ($in_row as $f) {
                if ($f->getId() != $field->getId()) {
                    $this->remove($f);
                }
            }
        }
        
        if ($type != 'period') {
            $field->setFieldType($type);
            $field->setInRow(false);

            if ($type == 'choice-radio') {
                $add_data["choices"] = ["oui"=>"oui","non"=>"non"];
                $field->setAdditionalData($add_data);
            }
        } else {
            $type = 'date';
            $order = $field->getFieldOrder();
            $field->setFieldType($type);
            $field->setInRow($order);

            $field2 = clone $field;
            $field2->setLabel('à');
            $field2->setFieldOrder($order+1);
            $this->em->persist($field2);
        }
        $this->save();

        return $field;
    }

    public function addNewField($new_field, SiteFormSetting $site_form_setting)
    {
        if (!is_null($site_form_setting)
            &&
            (
                is_int($site_form_setting->getCustomFieldAllowed())
                && $site_form_setting->getCustomFieldAllowed() > 0
            )
        ) {
            $type = ($new_field['field_type']=="alphanum")?"text":$new_field['field_type'];


            if ($type != 'period') {
                $field = new SiteFormFieldSetting();
                $field->setSiteFormSetting($site_form_setting)
                        ->setFieldType($type)
                        ->setMandatory($new_field['mandatory'])
                        ->setLabel($new_field['label'])
                        ->setFieldOrder(100)
                        ->setPersonalizable(true); // big value, to put new field at the bottom
                if (isset($new_field['level'])) {
                    $field->setLevel($new_field['level']);
                    $order = $this->getRepository()->findLastOrder($field->getSiteFormSetting()->getId(), $new_field['level']);
                    $field->setFieldOrder($order);
                }

                if (array_key_exists('choices', $new_field)) {
                    $add_data["choices"] = $new_field["choices"];
                    $field->setAdditionalData($add_data);
                }
                $site_form_setting->addSiteFormFieldSetting($field);
                $this->em->persist($field);
            } else {
                $field = new SiteFormFieldSetting();
                $field->setSiteFormSetting($site_form_setting)
                        ->setFieldType('date')
                        ->setMandatory($new_field['mandatory'])
                        ->setLabel($new_field['label'])
                        ->setFieldOrder(100)
                        ->setPersonalizable(true); // big value, to put new field at the bottom
                if (isset($new_field['level'])) {
                    $field->setLevel($new_field['level']);
                    $order = (int) $this->getRepository()->findLastOrder($field->getSiteFormSetting()->getId(), $new_field['level']);
                    $field->setFieldOrder($order)
                          ->setInRow($order);
                }

                $field2 = clone $field;
                $field2->setLabel('à')
                    ->setFieldOrder($order+1);

                $site_form_setting->addSiteFormFieldSetting($field);
                $this->em->persist($field);
                $this->em->persist($field2);
            }

            $this->save();

            return $field;
        }
    }

    /**
     * [deleteField delete field]
     *
     * @param  array  $delete_fields
     * @param  SiteFormSetting $site_form_setting
     */
    public function deleteField($delete_fields, SiteFormSetting $site_form_setting, $no_adjust_allowed = false)
    {
        $field_to_delete_list = ('' != $delete_fields)
                                    ? explode(',', $delete_fields)
                                    : array();
        if (!empty($field_to_delete_list)) {
            foreach ($field_to_delete_list as $field_to_delete_id) {
                $field = $this->getRepository()->findOneById($field_to_delete_id);
                if (!is_null($field)) {
                    if (is_null($field->getInRow())) {
                        $this->remove($field);
                    } else {
                        $row = $field->getInRow();
                        $level = $field->getLevel();
                        $form_setting_id = $field->getSiteFormSetting()->getId();

                        $in_row = $this->getRepository()->findAllInRow($row, $form_setting_id, $level);
                        foreach ($in_row as $f) {
                            $this->remove($f);
                        }
                    }
                    if (!$no_adjust_allowed) {
                        $site_form_setting->setCustomFieldAllowed($site_form_setting->getCustomFieldAllowed() + 1);
                    }
                }
            }
        }
    }
    
    public function getArrangedFields(SiteFormSetting $site_form_setting)
    {
        $site_form_field_setting = $site_form_setting->getSiteFormFieldSettings();
        $arranged_field = array();

        foreach ($site_form_field_setting as $field) {
            $arranged_field[$field->getLevel()][] = $field;
        }
        
        return $arranged_field;
    }

    public function rechargeDefaultFieldFor(Program $program, $site_form_type, $level)
    {
        $site_form_setting = $this->em->getRepository('AdminBundle:SiteFormSetting')->findAllDefaultFields($program, $site_form_type);
        $default_fields = $site_form_setting->getSiteFormFieldSettings();
        
        foreach ($default_fields as $field) {
            $field_clone = clone $field;
            $field_clone->setLevel($level);
            //dump($field_clone);
            $this->em->persist($field_clone);
        }

        $this->save();
        $this->em->clear(SiteFormSetting::class);

        return;
    }

    public function removeFieldsForLevel(Program $program, $site_form_type, $level)
    {
        $site_form_setting = $this->em->getRepository('AdminBundle:SiteFormSetting')->findByProgramAndTypeAndLevelWithField($program, $site_form_type, $level);
        $fields = $site_form_setting->getSiteFormFieldSettings();

        foreach ($fields as $field) {
            $this->remove($field);
        }
        $this->save();

        return;
    }

    public function getMaxLevel($program, $site_form_type)
    {
        return $this->em->getRepository('AdminBundle:SiteFormSetting')->findMaxLevel($program, $site_form_type);
    }

    public function remove(SiteFormFieldSetting $field)
    {
        return $this->em->remove($field);
    }

    public function save()
    {
        return $this->em->flush();
    }
}
