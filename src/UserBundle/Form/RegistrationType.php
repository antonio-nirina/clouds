<?php

namespace UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('civility',ChoiceType::class,
            ["choices"=>[
                "Mme"=>"Mme",
                "M."=>"Mr."
            ],
            "label"=>"civilité",
            'choices_as_values' => true,
            'multiple'=>false,
            'expanded'=>true
        ]);
        $builder->add('firstname',TextType::class,["label"=>"nom*"]);
        $builder->remove('username');
        $builder->add('name',TextType::class,["label"=>"prénom*"]);
        $builder->add('email', RepeatedType::class, array(
            'type' => EmailType::class,
            'required' => true,
            'first_options'  => array('label' => 'email*'),
            'second_options' => array('label' => 'confirmation e-email*'),
        ));
        $builder->add('phone',TextType::class,["label"=>"téléphone*"]);
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options'  => array('label' => 'mot de passe*'),
            'second_options' => array('label' => 'confirmation mot de passe*'),
        ));
        $builder->add('societe',TextType::class,["label"=>"Société*"]);
        $builder->add('address_1',TextType::class,["label"=>"adresse postale*"]);
        $builder->add('postal_code',TextType::class,["label"=>"code postal*"]);
        $builder->add('city',TextType::class,["label"=>"ville*"]);
        $builder->add('accepte',ChoiceType::class,           
            [
            "mapped" => false,
            "choices"=>[
                "M"=>"M."
            ],
            'choice_attr' => array(
                'M' => array('class' => 'checkbox'),
            ),
            "label"=>"acceptation du règlement*",
            'choices_as_values' => true,
            'multiple'=>false,
            'expanded'=>true
        ]);
        $builder->add('recaptcha', EWZRecaptchaType::class,array(
        'attr' => array(
            'options' => array(
                'theme' => 'light',
                'type'  => 'image',
                'size'  => 'normal',
                'defer' => true,
                'async' => true,
            )
        ),
        ));

    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}