<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramPointAttributionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'point_attribution_setting', CollectionType::class, array(
                    'entry_type' => $options['entry_type_class'],
                    'entry_options' => array(
                        'label' => false
                    )
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
            'entry_type_class' => AbstractType::class
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'program_point_attribution';
    }
}
