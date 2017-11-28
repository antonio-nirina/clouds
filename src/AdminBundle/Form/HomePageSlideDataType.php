<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Form\HomePageSlideType;
use AdminBundle\Entity\HomePageData;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HomePageSlideDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('home_page_slides', CollectionType::class, array(
            'entry_type' => HomePageSlideType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
            'allow_delete' => true,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => HomePageData::class,
        ));
    }
}
