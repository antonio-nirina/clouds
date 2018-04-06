<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use AdminBundle\Form\SondagesQuizQuestionsType;
use AdminBundle\Entity\SondagesQuizQuestionnaireInfos;
use Doctrine\ORM\EntityManager;


class SondagesQuizQuestionnaireInfosType extends AbstractType
{
	protected $em;

	public function __construct(EntityManager $em) 
	{
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type_sondages_quiz', ChoiceType::class, array(
						'choices' => array(
							'créer un sondage' => '1',
							'créer un quiz' => '2'
						),
						'choices_as_values' => true,
						'multiple' => false,
						'expanded' => true,
					))
				->add('titre_questionnaire', TextType::class)
				->add('description_questionnaire', TextareaType::class)
				->add('sondages_quiz_questions', CollectionType::class, array(
					'label' => false,
					'entry_type' => SondagesQuizQuestionsType::class,
					'allow_add' => true,
					'allow_delete' => true,
					'prototype' => true,
					'prototype_name' => '__opt_questions__'
				  ))
				->add('authorized_role',ChoiceType::class, array(
						'choices' => $this->getAllRoles(),
						'choices_as_values' => true,
						'multiple' => false,
						'expanded' => false,
					));
    }
	
	/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SondagesQuizQuestionnaireInfos::class,
        ));
    }

	/**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sondages_quiz_questionnaire_infos';
    }

    protected function getAllRoles()
    {
    	 $roles = $this->em->getRepository('AdminBundle\Entity\Role')->findAll();
    	 foreach ($roles as $key => $value) {
    	 	$role[$value->getName()] = $value->getId();
    	 }

    	 return $role;
    }

}
