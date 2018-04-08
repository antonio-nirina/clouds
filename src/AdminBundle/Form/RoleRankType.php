<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class RoleRankType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active', CheckboxType::class, array())
            ->add('rank', HiddenType::class, array())
            ->add('name', TextType::class, array())
            ->add(
                'gain',
                TextType::class,
                array(
                    'constraints' => array(
                        new Range(
                            array(
                                "min" => 0,
                                "max" => 100,
                                "minMessage" => "Pour les récompenses, entrer une valeur entre 0 à 100%",
                                "maxMessage" => "Pour les récompenses, entrer une valeur entre 0 à 100%",
                                "invalidMessage" => "Entrer des valeurs numériques pour les récompenses"
                            )
                        ),
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
            'data_class' => Role::class,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'role_rank_form';
    }
}
