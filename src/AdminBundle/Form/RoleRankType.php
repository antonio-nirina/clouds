<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleRankType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rank', HiddenType::class, array())
                ->add('name', TextType::class, array())
                ->add('gain', TextType::class, array(
                    'constraints' => array(
                    )
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Role::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'role_rank_form';
    }
}
