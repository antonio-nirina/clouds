<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\SiteTableNetworkSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteTableNetworkSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('has_classment', CheckboxType::class)
                ->add('has_like', CheckboxType::class)
                ->add('has_facebook', CheckboxType::class)
                ->add('facebook_link', TextType::class)
                ->add('has_linkedin', CheckboxType::class)
                ->add('linkedin_link', TextType::class)
                ->add('has_twitter', CheckboxType::class)
                ->add('twitter_link', TextType::class)
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SiteTableNetworkSetting::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_table_network';
    }
}
