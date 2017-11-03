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
    public function adjustFieldOrder($field_order, $current_fields)
    {
        $field_order = json_decode($field_order);
        $field_order = array_flip($field_order);

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
    public function addNewFields($new_field_list, SiteFormSetting $site_form_setting, $level = false)
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
                    if ($level) {
                        $field->setLevel($level);
                    }

                    if (array_key_exists('choices', $new_field)) {
                        $choices = array_map('strval', (array)$new_field->choices);
                        $choices = array_map('strval', array_flip($choices)); // VALUE is the same as KEY
                        $add_data["choices"] = $choices;
                        $field->setAdditionalData($add_data);
                    }
                    $site_form_setting->addSiteFormFieldSetting($field);
                    $this->em->persist($field);

                    $site_form_setting->setCustomFieldAllowed(($site_form_setting->getCustomFieldAllowed()) - 1);
                }
            }
        }
    }

    /**
     * [deleteField delete field]
     *
     * @param  array  $delete_fields
     * @param  SiteFormSetting $site_form_setting
     */
    public function deleteField($delete_fields, SiteFormSetting $site_form_setting)
    {
        $field_to_delete_list = ('' != $delete_fields)
                                    ? explode(',', $delete_fields)
                                    : array();
        if (!empty($field_to_delete_list)) {
            foreach ($field_to_delete_list as $field_to_delete_id) {
                $field = $this->getRepository()->findOneById($field_to_delete_id);
                if (!is_null($field)) {
                    $this->remove($field);
                    $site_form_setting->setCustomFieldAllowed($site_form_setting->getCustomFieldAllowed() + 1);
                }
            }
        }
    }

    /**
     * Réinitialisation des champs via champs par défauts
     *
     * @param  Program $program
     * @param  string  $site_form_type
     * @param  int  $level
     */
    public function rechargeDefaultFieldFor(Program $program, $site_form_type, $level)
    {
        $site_form_setting_repo = $this->em->getRepository('AdminBundle:SiteFormSetting');
        $site_form_setting = $site_form_setting_repo->findByProgramAndTypeAndLevelWithField(
            $program,
            $site_form_type,
            $level //modifier pour chaque produit
        );

        if (empty($site_form_setting)) {
            $site_form_setting = $site_form_setting_repo->findAllDefaultFields($program, $site_form_type);
            $default_fields = $site_form_setting->getSiteFormFieldSettings();

            foreach ($default_fields as $field) {
                $field_clone = clone $field;
                $field_clone->setLevel($level);
                $this->em->persist($field_clone);
            }

            $this->save();

            $site_form_setting = $site_form_setting_repo->findByProgramAndTypeAndLevelWithField(
                $program,
                $site_form_type,
                $level //modifier pour chaque produit
            );
        }

        return $site_form_setting[0];
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
