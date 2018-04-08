<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Program;
use AdminBundle\Form\RoleRankType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramRankType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'roles', CollectionType::class, array(
            'entry_type' => RoleRankType::class,
            'entry_options' => array(
                'required' => false,
                'label' => false
            ),
            'label' => false
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
        return 'program_rank_form';
    }
}
