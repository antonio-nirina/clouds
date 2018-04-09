<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FirstChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('current_password'); // sans définition du mot de passe actuel
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ChangePasswordFormType';
    }


    /**
     * {@inheritdoc}
     */
    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults(array(
    //         'data_class' => 'AppBundle\Entity\User'
    //     ));
    // }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_first_change_password';
    }
}
