<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AdminBundle\Form\SondagesQuizQuestionsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class SondagesQuizReponsesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reponses', TextType::class)
				->add('est_bonne_reponse', CheckboxType::class, array(
						'label' => 'bonne réponse',
						'required' => false,
					))
				->add('ordre', HiddenType::class);
    }
	
	/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\SondagesQuizReponses'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sondages_quiz_reponses';
    }
}
