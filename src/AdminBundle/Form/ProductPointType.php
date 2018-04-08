<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AdminBundle\DTO\ProductPointSettingData;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Form\ProductPointUnitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'product_point_setting_list', CollectionType::class, array(
            'entry_type' => ProductPointUnitType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
            'allow_delete' => true,
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => ProductPointSettingData::class,
            )
        );
    }
}