<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Exception\NoRelatedProgramException;
use AdminBundle\Manager\ProgramManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Holding Form Fields for manipulating action button
 */
class ActionButtonType extends AbstractType
{
    private $program_manager;
    private $em;
    private $url_generator;

    public function __construct(
        ProgramManager $program_manager,
        EntityManager $em,
        UrlGeneratorInterface $url_generator
    ) {
        $this->program_manager = $program_manager;
        $this->em = $em;
        $this->url_generator = $url_generator;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action_button_text', TextType::class)
            ->add('action_button_text_color', TextType::class)
            ->add('action_button_background_color', TextType::class)
            ->add('action_button_target_url', TextType::class)
            ->add('action_button_target_page', ChoiceType::class, array(
                'choices' =>  $this->retrieveTargetPageList(),
                'placeholder' => 'POINTER SUR',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
        ));
    }

    /**
     * Retrieve target page list to be used by news post
     *
     * @return array
     */
    private function retrieveTargetPageList()
    {
        $target_list = array();
        $program = $this->program_manager->getCurrent();
        if (empty($program)) {
            throw new NoRelatedProgramException();
        }

        // put standard pages in options
        $page_list = $this->em->getRepository('AdminBundle\Entity\SitePagesStandardSetting')->findBy(
            array('program' => $program, 'status_page' => true)
        );
        if (!empty($page_list)) {
            foreach ($page_list as $page) {
                $target_list[$page->getNomPage()] = $this->url_generator
                    ->generate('beneficiary_home_pages_standard_slug', array('slug' => $page->getSlug())) ;
            }

        }

        // put survey and quiz page and questionnaires in options
        $survey_and_quiz = $this->em->getRepository('AdminBundle\Entity\SondagesQuiz')
            ->findOneBy(array('program' => $program));
        if (!is_null($survey_and_quiz)) {
            $target_list[$survey_and_quiz->getTitre()] = $this->url_generator
                ->generate(
                    'beneficiary_home_pages_sondages_quiz_slug',
                    array('slug' => $survey_and_quiz->getSlug())
                );
            $survey_and_quiz_questionnaire_list = $this->em
                ->getRepository('AdminBundle\Entity\SondagesQuizQuestionnaireInfos')
                ->findBy(
                    array('sondages_quiz' => $survey_and_quiz)
                );
            if (!empty($survey_and_quiz_questionnaire_list)) {
                foreach ($survey_and_quiz_questionnaire_list as $survey_and_quiz_questionnaire) {
                    $target_list[$survey_and_quiz_questionnaire->getTitreQuestionnaire()]
                        = $this->url_generator->generate(
                            'beneficiary_home_pages_sondages_quiz_slug',
                            array(
                                'slug' => $survey_and_quiz->getSlug(),
                                'id' => $survey_and_quiz_questionnaire->getId()
                            )
                        );
                }
            }
        }

        return $target_list;
    }
}