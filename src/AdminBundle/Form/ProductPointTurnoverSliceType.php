<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Entity\PointAttributionSetting;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\DTO\ProductPointTurnoverSliceData;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\CallbackTransformer;
use AdminBundle\Component\PointAttribution\PointAttributionStatus;
use AdminBundle\Form\ProductPointTurnoverSliceUnitType;

class ProductPointTurnoverSliceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('product_point_attribution_settings', CollectionType::class, array(
            'entry_type' => ProductPointTurnoverSliceUnitType::class,
            'entry_options' => array('label' => false),
        ))
            ->add('status', CheckboxType::class);

        $builder->get('status')->addModelTransformer(new CallBackTransformer(
            function ($status) {
                return (PointAttributionStatus::ON == $status) ? true : false;
            },
            function ($status_boolean_value) {
                return (true == $status_boolean_value) ? PointAttributionStatus::ON : PointAttributionStatus::OFF;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProductPointTurnoverSliceData::class,
        ));
    }
}