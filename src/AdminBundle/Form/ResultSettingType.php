<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\ResultSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('monthly', ChoiceType::class, array(
                    'choices'  => array(
                        'Oui' => true,
                        'Non' => false),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true
                ))
                ->add('by_product', CheckboxType::class, array(
                        'label'    => '',
                        'required' => false,))
                ->add('by_rank', CheckboxType::class, array(
                        'label'    => '',
                        'required' => false,));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ResultSetting::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'resultSetting';
    }
}
