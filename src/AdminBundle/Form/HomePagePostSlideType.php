<?php
namespace AdminBundle\Form;

use AdminBundle\Entity\HomePagePostSlide;
use AdminBundle\Form\BasicSlideType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomePagePostSlideType extends BasicSlideType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('message', TextType::class)
            ->add('video_url', TextType::class)
            ->add('slide_type', HiddenType::class)
            ->add('image_target_url', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => HomePagePostSlide::class,
            )
        );
    }
}
