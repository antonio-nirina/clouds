<?php
namespace AdminBundle\Service\SiteForm;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Component\SiteForm\FieldType;
use AdminBundle\Entity\SiteFormFieldSetting;
use Symfony\Component\Validator\Constraints\Type;

class SiteFormBuilder
{
    private $form_factory;
    private $form;

    const MANDATORY_FIELD_SIGN = "*";

    public function __construct(FormFactoryInterface $form_factory)
    {
        $this->form_factory = $form_factory;
        $this->form = $this->form_factory->create();
    }

    public function build(Collection $field_list)
    {
        if (empty($field_list)) {
            return $this->form;
        }
        foreach ($field_list as $field) {
            $this->addField($field);
        }

        return $this->form;
    }

    private function addField(SiteFormFieldSetting $field)
    {
        $constraints = array();
        if (true == $field->getMandatory()) {
            array_push($constraints, new NotBlank());
        }

        switch ($field->getFieldType()) {
            case FieldType::TEXT:
                $current_constraints = array();
                $this->configureTextField(
                    TextType::class,
                    $current_constraints,
                    $constraints,
                    $field
                );
                break;

            case FieldType::ALPHA_TEXT:
                $current_constraints = array(
                    new Type(array("type" => "alpha")),
                );
                $this->configureTextField(
                    TextType::class,
                    $current_constraints,
                    $constraints,
                    $field
                );
                break;

            case FieldType::NUM_TEXT:
                $current_constraints = array();
                $this->configureTextField(
                    IntegerType::class,
                    $current_constraints,
                    $constraints,
                    $field
                );
                break;

            case FieldType::ALPHANUM_TEXT:
                $current_constraints = array(
                    new Type(array("type" => "alnum")),
                );
                $this->configureTextField(
                    TextType::class,
                    $current_constraints,
                    $constraints,
                    $field
                );
                break;

            case FieldType::EMAIL:
                $current_constraints = array();
                $this->configureTextField(
                    EmailType::class,
                    $current_constraints,
                    $constraints,
                    $field
                );
                break;

            case FieldType::CHOICE_RADIO:
                $current_constraints = array();
                $this->configureChoiceField(
                    ChoiceType::class,
                    $current_constraints,
                    $constraints,
                    $field
                );
                break;
        }
    }

    private function configureTextField(
        $form_field_type,
        array $current_constraints,
        array $mandatory_constraint,
        SiteFormFieldSetting $field
    ) {
        $constraints = array_merge($current_constraints, $mandatory_constraint);
        $label = $field->getLabel();
        if (!empty($mandatory_constraint)) {
            $label = $label.' '.self::MANDATORY_FIELD_SIGN;
        }
        $this->form->add($field->getId(), $form_field_type, array(
            "label" => $label,
            "constraints" => $constraints,
        ));
    }

    public function configureChoiceField(
        $form_field_type,
        array $current_constraints,
        array $mandatory_constraint,
        SiteFormFieldSetting $field
    ) {
        $constraints = array_merge($current_constraints, $mandatory_constraint);
        $label = $field->getLabel();
        if (!empty($mandatory_constraint)) {
            $label = $label.' '.self::MANDATORY_FIELD_SIGN;
        }
        $this->form->add($field->getId(), $form_field_type, array(
            "label" => $label,
            "constraints" => $constraints,
            "choices" => $field->getAdditionalData()["choices"],
            "expanded" => true,
            "multiple" => false,
        ));
    }
}