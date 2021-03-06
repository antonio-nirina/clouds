<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\PointAttributionSetting;
use Symfony\Component\Form\CallbackTransformer;
use AdminBundle\Component\PointAttribution\PointAttributionStatus;

class ProductPointTurnoverProportionalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('min_value', TextType::class)
            ->add('max_value', TextType::class)
            ->add('gain', TextType::class)
            ->add('status', CheckboxType::class);

        $builder->get('status')->addModelTransformer(
            new CallBackTransformer(
                function ($status) {
                    return (PointAttributionStatus::ON == $status) ? true : false;
                },
                function ($status_boolean_value) {
                    return (true == $status_boolean_value) ? PointAttributionStatus::ON : PointAttributionStatus::OFF;
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => PointAttributionSetting::class,
            )
        );
    }
}
