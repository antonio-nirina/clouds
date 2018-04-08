<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use AdminBundle\Validator\Constraints\FileExtensionConstraint;

class RegistrationImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'registration_data',
            FileType::class,
            array(
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
