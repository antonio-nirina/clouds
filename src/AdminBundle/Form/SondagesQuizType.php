<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\SondagesQuiz;
use AdminBundle\Validator\Constraints\FileExtensionConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SondagesQuizType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_menu', TextType::class)
            ->add('titre', TextType::class)
            ->add(
                'image',
                FileType::class,
                array(
                    "required" => false
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
            'data_class' => SondagesQuiz::class,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sondages_quiz';
    }
}
