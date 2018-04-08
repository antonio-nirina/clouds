<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\ResultSetting;
use AdminBundle\Validator\Constraints\FileExtensionConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResultSettingUploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'uploaded_file', FileType::class, array(
            "label" => "",
            "constraints" => array(
                new FileExtensionConstraint('csv'),
                new NotBlank(),
                new File(
                    array(
                        "mimeTypes" => "text/plain",
                        "maxSize" => "3M"
                    )
                ),
            ),
            )
        );
    }
}
