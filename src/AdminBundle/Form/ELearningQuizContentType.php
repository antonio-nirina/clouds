<?php
namespace AdminBundle\Form;

use AdminBundle\Form\ELearningContentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use AdminBundle\Manager\ProgramManager;
use AdminBundle\Exception\NoRelatedProgramException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\ELearningQuizContent;

/**
 * Form type for e-learning quiz content
 */
class ELearningQuizContentType extends ELearningContentType
{
    private $em;
    private $program_manager;

    public function __construct(EntityManager $em, ProgramManager $program_manager)
    {
        $this->em = $em;
        $this->program_manager = $program_manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('quiz', EntityType::class, array(
            'class' => 'AdminBundle:SondagesQuizQuestionnaireInfos',
            'choices' => $this->retrieveQuiz(),
            'choice_label' => 'titre_questionnaire',
            'placeholder' => 'SÉLECTIONNEZ UN QUIZ',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ELearningQuizContent::class,
        ));
    }

    /**
     * Retrieve all quiz corresponding to current program
     */
    public function retrieveQuiz()
    {
        $program = $this->program_manager->getCurrent();
        if (empty($program)) {
            throw new NoRelatedProgramException();
        }
        $quiz_list = $this->em->getRepository('AdminBundle\Entity\SondagesQuizQuestionnaireInfos')
            ->findQuizByProgram($program);

        return $quiz_list;
    }
}