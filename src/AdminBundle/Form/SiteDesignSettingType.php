<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\SiteDesignSetting;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Validator\Constraints\FileExtensionConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SiteDesignSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'police',
            ChoiceType::class,
            array(
                            'choices'  => array(
                                'Roboto' => "Roboto",
                                'Open Sans' => "OpenSans",
                                'Lato' => "Lato",
                                'Adamina' => "Adamina",
                                'Petrona' => "Petrona",
                                'Comfortaa' => "Comfortaa"),
                            'expanded' => true,
                            'multiple' => false
            )
        )
            ->add(
                'colors',
                CollectionType::class,
                array(
                    "entry_type" => TextType::class,
                    "entry_options" => array(
                        "required" => false
                    )
                )
            )
            ->add(
                "body_background",
                FileType::class,
                array(
                    'required' => false,
                    'constraints' => array(
                    )
                )
            )
            ->add(
                'logo_path',
                FileType::class,
                array(
                    'required' => false,
                    "constraints" => array(
                    )
                )
            )
            ->add('logo_name', TextType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => SiteDesignSetting::class,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site_design_setting';
    }
}
