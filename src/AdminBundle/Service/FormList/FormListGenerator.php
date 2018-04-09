<?php
namespace AdminBundle\Service\FormList;

use Symfony\Component\Form\FormFactory;

class FormListGenerator
{
    const DEFAULT_FORM_NAME_PREFIX = 'default_form_name_';

    protected $form_factory;
    protected $form_name_prefix;

    public function __construct(FormFactory $form_factory)
    {
        $this->form_factory = $form_factory;
        $this->defineFormNamePrefix();
    }

    public function defineFormNamePrefix()
    {
        $this->form_name_prefix = self::DEFAULT_FORM_NAME_PREFIX;
    }

    public function generateFormList($data_list, $form_type)
    {
        $form_list = array();
        if (!empty($data_list)) {
            $form_index = 1;
            foreach ($data_list as $data) {
                $form = $this->form_factory->createNamed(
                    $this->form_name_prefix . $form_index,
                    $form_type,
                    $data
                );
                array_push($form_list, $form);
                $form_index++;
            }
        }

        return $form_list;
    }


    public function generateFormViewList($form_list)
    {
        $form_view_list = array();
        if (!empty($form_list)) {
            foreach ($form_list as $form) {
                array_push($form_view_list, $form->createView());
            }
        }

        return $form_view_list;
    }
}
