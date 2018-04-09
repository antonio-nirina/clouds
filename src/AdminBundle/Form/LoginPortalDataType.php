<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\LoginPortalData;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Form\LoginPortalSlideType;
use Symfony\Component\Validator\Constraints\Valid;

class LoginPortalDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class)
            ->add('text', TextareaType::class)
            ->add(
                'login_portal_slides',
                CollectionType::class,
                array(
                'entry_type' => LoginPortalSlideType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => LoginPortalData::class,
            )
        );
    }
}
