<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\ComEmailTemplateContent;

class ComEmailTemplateContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text_content', TextareaType::class)
            ->add('image', FileType::class, array('data_class' => null))
            ->add('content_type', HiddenType::class)
            ->add('action_button_text', TextType::class)
            ->add('action_button_url', TextType::class)
            ->add('action_button_background_color', TextType::class)
            ->add('action_button_text_color', TextType::class)
            ->add('content_order', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ComEmailTemplateContent::class,
        ));
    }
}