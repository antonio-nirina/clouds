<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Entity\PointAttributionSetting;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\DTO\ProductPointTurnoverSliceData;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductPointTurnoverSliceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('product_points', CollectionType::class, array(
            'entry_type' => PointAttributionSetting::class,
            'entry_options' => array('label' => false),
        ))
        ->add(
            'status',
            ChoiceType::class,
            array(
                'multiple' => true,
                'expanded' => true,
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProductPointTurnoverSliceData::class,
        ));
    }
}