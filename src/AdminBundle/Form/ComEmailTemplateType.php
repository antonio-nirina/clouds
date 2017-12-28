<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\ComEmailTemplate;
use AdminBundle\Form\ComEmailTemplateContentType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ComEmailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('logo', FileType::class)
            ->add('logo_alignment', ChoiceType::class, array(
                'choices' => array(
                    'centré' => TemplateLogoAlignment::CENTER,
                    'ferré à gauche' => TemplateLogoAlignment::LEFT,
                    'ferré à droite' => TemplateLogoAlignment::RIGHT,
                    'étendue' => TemplateLogoAlignment::EXPANDED,
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('action_button_text', TextType::class)
            ->add('action_button_url', TextType::class)
            ->add('action_button_background_color', TextType::class)
            ->add('action_button_text_color', TextType::class)
            ->add('footer_list_state', CheckboxType::class)
            ->add('footer_company_state', CheckboxType::class)
            ->add('footer_unsubscribing_state', CheckboxType::class)
            ->add('footer_contact_state', CheckboxType::class)
            ->add('contents', CollectionType::class, array(
                'entry_type' => ComEmailTemplateContentType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ComEmailTemplate::class,
        ));
    }
}