<?php
namespace AdminBundle\Service\FormList;

use AdminBundle\Service\FormList\FormListGenerator;
use Symfony\Component\Form\FormFactory;

class EditoFormListGenerator extends FormListGenerator
{
    const FORM_NAME_PREFIX = 'edit_edito_form_';

    public function defineFormNamePrefix()
    {
        $this->form_name_prefix = self::FORM_NAME_PREFIX;
    }
}
