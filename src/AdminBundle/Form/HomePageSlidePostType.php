<?php

namespace AdminBundle\Form;

use AdminBundle\Form\HomePagePostType;
use AdminBundle\Form\HomePageSlideType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomePageSlidePostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'home_page_slides', CollectionType::class, array(
            'entry_type' => HomePageSlideType::class,
            'entry_options' => array('label' => false),
            'allow_add' => true,
            'allow_delete' => true,
            )
        );
    }

    public function getParent()
    {
        return HomePagePostType::class;
    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults(array(
    //         'data_class' => '',
    //     ));
    // }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return 'home_page_slide_post';
    }
}
