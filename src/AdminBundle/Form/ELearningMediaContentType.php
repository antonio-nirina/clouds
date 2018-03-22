<?php
namespace AdminBundle\Form;

use AdminBundle\Form\ELearningContentType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Form\ELearningGalleryImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\ELearningMediaContent;

/**
 * Form type for e-learning media content
 */
class ELearningMediaContentType extends ELearningContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('associated_file', FileType::class)
            ->add('video_url', TextType::class)
            ->add('images', CollectionType::class, array(
                'entry_type' => ELearningGalleryImageType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__image_name__',
            ))
            ->add('content_type', HiddenType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ELearningMediaContent::class,
        ));
    }
}