<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\CallbackTransformer;

abstract class BasicSlideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', FileType::class)
            ->add('slide_order', TextType::class)
            ->add('delete_image_command', HiddenType::class, array('mapped' => false));

        $builder->get('image')->addModelTransformer(
            new CallBackTransformer(
                function ($image) {
                    return null;
                },
                function ($image) {
                    return $image;
                }
            )
        );
    }
}
