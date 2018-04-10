<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\ELearningHomeBanner;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ELearningHomeBannerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('menuName', TextType::class)
            ->add('imageTitle', TextType::class)
            ->add('imageFile', FileType::class, array('data_class' => null))
            ->add('delete_image_command', HiddenType::class, array('mapped' => false));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => ELearningHomeBanner::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_elearninghomebanner';
    }

}
