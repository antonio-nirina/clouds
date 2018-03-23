<?php
namespace AdminBundle\Form;

use AdminBundle\Manager\ProgramManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\ELearning;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AdminBundle\Form\SelectingCommonDataType;
use AdminBundle\Form\ELearningMediaContentType;
use AdminBundle\Form\ELearningQuizContentType;
use AdminBundle\Form\ELearningButtonContentType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Form type for creating/editing e-learning
 */
class ELearningType extends SelectingCommonDataType
{
    public function __construct(ProgramManager $program_manager, EntityManager $em)
    {
        parent::__construct($program_manager, $em);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('title', TextType::class)
            ->add('main_text', TextareaType::class)
            ->add('media_contents', CollectionType::class, array(
                'entry_type' => ELearningMediaContentType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add('quiz_contents', CollectionType::class, array(
                'entry_type' => ELearningQuizContentType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
            ))
//            ->add('button_content', ELearningButtonContentType::class);
            ->add('button_contents', CollectionType::class, array(
                'entry_type' => ELearningButtonContentType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ELearning::class,
        ));
    }
}