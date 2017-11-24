<?php
namespace AdminBundle\Form;

use AdminBundle\Form\BasicSlideType;
use AdminBundle\Entity\LoginPortalSlide;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginPortalSlideType extends BasicSlideType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LoginPortalSlide::class,
        ));
    }
}