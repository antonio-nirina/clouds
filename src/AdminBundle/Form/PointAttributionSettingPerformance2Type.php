<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\PointAttributionSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class PointAttributionSettingPerformance2Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'min_value',
            TextType::class,
            array(
                        'label' => false,
                        "required" => false,
                        'constraints' => array(
                            new Type(
                                array(
                                    "type" => "numeric",
                                    "message" => "Entrez des valeurs numériques dans les champs"
                                )
                            )
                        )
            )
        )
            ->add(
                'max_value',
                TextType::class,
                array(
                        'label' => false,
                        "required" => false,
                        'constraints' => array(
                            new Type(
                                array(
                                    "type" => "numeric",
                                    "message" => "Entrez des valeurs numériques dans les champs"
                                )
                            )
                        )
                )
            )
            ->add(
                'gain',
                TextType::class,
                array(
                        'label' => false,
                        "required" => false,
                        'constraints' => array(
                            new Type(
                                array(
                                    "type" => "numeric",
                                    "message" => "Entrez des valeurs numériques dans les champs"
                                )
                            )
                        )
                )
            )
            ->add('status', HiddenType::class, array());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => PointAttributionSetting::class,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'point_attribution_form_performance_2';
    }
}
