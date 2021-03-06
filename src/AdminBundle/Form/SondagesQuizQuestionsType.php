<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Form\SondagesQuizReponsesType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SondagesQuizQuestionsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('questions', TextType::class)
            ->add('commentaire', TextareaType::class)
            ->add(
                'type_question',
                ChoiceType::class,
                array(
                'choices'  => array(
                'cases à cocher' => 1,
                'choix multiples' => 2,
                'échelle linéaire' => 3,
                'tableau à choix mutltiples' => 4,
                ),
                )
            )
            ->add(
                'est_reponse_obligatoire',
                CheckboxType::class,
                array(
                'label' => 'réponse obligatoire',
                'required' => false,
                )
            )
            ->add(
                'sondages_quiz_reponses',
                CollectionType::class,
                array(
                'entry_type' => SondagesQuizReponsesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__opt_reponse__'
                )
            )
            ->add('ordre', HiddenType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AdminBundle\Entity\SondagesQuizQuestions'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sondages_quiz_questions';
    }
}
