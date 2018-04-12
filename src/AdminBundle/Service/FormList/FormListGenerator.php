<?php
namespace AdminBundle\Service\FormList;

use Symfony\Component\Form\FormFactory;

class FormListGenerator
{
    const DEFAULT_FORM_NAME_PREFIX = 'default_form_name_';

    protected $formFactory;
    protected $formNamePrefix;

    /**
     * FormListGenerator constructor.
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->defineFormNamePrefix();
    }

    /**
     * prefix form
     */
    public function defineFormNamePrefix()
    {
        $this->formNamePrefix = self::DEFAULT_FORM_NAME_PREFIX;
    }

    /**
     * @param $data_list
     * @param $form_type
     * @return array
     */
    public function generateFormList($data_list, $form_type)
    {
        $formList = array();
        if (!empty($data_list)) {
            $form_index = 1;
            foreach ($data_list as $data) {
                $form = $this->formFactory->createNamed(
                    $this->formNamePrefix . $form_index,
                    $form_type,
                    $data
                );
                array_push($formList, $form);
                $form_index++;
            }
        }

        return $formList;
    }


    /**
     * @param $formList
     * @return array
     */
    public function generateFormViewList($formList)
    {
        $formViewList = array();
        if (!empty($formList)) {
            foreach ($formList as $form) {
                array_push($formViewList, $form->createView());
            }
        }

        return $formViewList;
    }
}
