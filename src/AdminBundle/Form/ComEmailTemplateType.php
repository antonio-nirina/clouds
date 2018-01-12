<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\ComEmailTemplate;
use AdminBundle\Form\ComEmailTemplateContentType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ComEmailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('logo', FileType::class, array('data_class' => null))
            ->add('logo_alignment', ChoiceType::class, array(
                'choices' => array(
                    'center' => TemplateLogoAlignment::CENTER,
                    'left' => TemplateLogoAlignment::LEFT,
                    'right' => TemplateLogoAlignment::RIGHT,
                    'expanded' => TemplateLogoAlignment::EXPANDED,
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('action_button_text', TextType::class)
            ->add('action_button_url', TextType::class)
            ->add('action_button_background_color', TextType::class)
            ->add('action_button_text_color', TextType::class)
            ->add('email_color', TextType::class)
            ->add('background_color', TextType::class)
            ->add('footer_company_info', TextType::class)
            ->add('footer_contact_info', TextType::class)
            ->add('footer_unsubscribing_text', TextType::class)
            ->add('footer_additional_info', TextType::class)
            ->add('contents', CollectionType::class, array(
                'entry_type' => ComEmailTemplateContentType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
            ))
            ->add('template_model', HiddenType::class)
            ->add('delete_logo_image_command', HiddenType::class, array('mapped' => false))
            ->add('delete_contents_image_command', HiddenType::class, array('mapped' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ComEmailTemplate::class,
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        usort(
            $view['contents']->children,
            function ($a, $b) {
                $aOrder = $a->vars['data']->getContentOrder();
                $bOrder = $b->vars['data']->getContentOrder();
                if ($aOrder == $bOrder) {
                    return 0;
                }

                return ($aOrder < $bOrder) ? -1 : 1;
            }
        );
    }
}