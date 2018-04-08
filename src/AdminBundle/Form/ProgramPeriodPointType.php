<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramPeriodPointType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'period_point_setting', CollectionType::class, array(
                            'entry_type' => PeriodPointSettingType::class,
                            'entry_options' => array(),
                            'label' => false,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => Program::class,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'program_period_point';
    }
}
