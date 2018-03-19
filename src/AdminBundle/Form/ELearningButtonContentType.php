<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use AdminBundle\Form\ActionButtonType;
use AdminBundle\Entity\ELearningButtonContent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Form\ELearningContentType;

/**
 * Form type for e-learning button content
 */
class ELearningButtonContentType extends ELearningContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('action_button_data', ActionButtonType::class, array(
            'data_class' => ELearningButtonContent::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ELearningButtonContent::class,
        ));
    }
}