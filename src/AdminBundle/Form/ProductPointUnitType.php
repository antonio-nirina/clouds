<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AdminBundle\Form\ProductPointTurnoverProportionalType;
use AdminBundle\Form\ProductPointTurnoverSliceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\DTO\ProductPointSettingUnitData;
use Symfony\Component\Form\Extension\Core\Type\HiddenType
    ;

class ProductPointUnitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('product_point_turnover_proportional', ProductPointTurnoverProportionalType::class)
            ->add('product_point_turnover_slice', ProductPointTurnoverSliceType::class)
            ->add('product_group', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => ProductPointSettingUnitData::class,
            )
        );
    }
}
