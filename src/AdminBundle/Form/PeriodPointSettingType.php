<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\PeriodPointSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class PeriodPointSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gain', CollectionType::class, array(
                        "entry_type" => TextType::class,
                        "entry_options" => array(
                            'required' => false,
                            'constraints' => array(
                                new Range(array(
                                    "min" => 0,
                                    "max" => 1000,
                                    "maxMessage" => "Vous avez entré une valeur trop élevé",
                                    "minMessage" => "N'accepte pas les valeurs négatives",
                                    'invalidMessage' => "Entrer des valeurs numériques à la place des NaN"
                                )),
                            )
                        ),
                        "label" => false
                    ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PeriodPointSetting::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'period_point_setting';
    }
}
