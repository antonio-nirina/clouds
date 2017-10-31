<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FormStructureDeclarationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('current-field-list', HiddenType::class)
            ->add('new-field-list', HiddenType::class)
            ->add('field-order', HiddenType::class)
            ->add('delete-field-action-list', HiddenType::class)
            ->add('validation-required', HiddenType::class)
            ->add('pieces-required', HiddenType::class)
            ->add('text-head-required', HiddenType::class)
            ->add('text-head', HiddenType::class);
    }
}
