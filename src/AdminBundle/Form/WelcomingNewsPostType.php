<?php
namespace AdminBundle\Form;

use AdminBundle\Form\NewsPostType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type for creating/editing welcoming news post
 */
class WelcomingNewsPostType extends NewsPostType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('programmed_publication_state')
            ->remove('programmed_publication_datetime');
    }
}
