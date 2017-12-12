<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\PointAttributionSetting;

class ProductPointTurnoverSliceUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('min_value', TextType::class)
            ->add('max_value', TextType::class)
            ->add('gain', TextType::class)
            ->add('status', ChoiceType::class, array(
                'multiple' => true,
                'expanded' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PointAttributionSetting::class,
        ));
    }
}