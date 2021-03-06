<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\Entity\NewsPost;
use AdminBundle\Form\HomePagePostType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Manager\ProgramManager;
use AdminBundle\Exception\NoRelatedProgramException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AdminBundle\Form\SelectingCommonDataType;

/**
 * Form type for creating/editing news post
 */
class NewsPostType extends SelectingCommonDataType
{
    private $container;
    private $url_generator;

    public function __construct(
        ContainerInterface $container,
        ProgramManager $program_manager,
        EntityManager $em,
        UrlGeneratorInterface $url_generator
    ) {
        parent::__construct($program_manager, $em);
        $this->container = $container;
        $this->url_generator = $url_generator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('home_page_post', HomePagePostType::class)
            ->add('action_button_state', CheckboxType::class)
            ->add('action_button_text', TextType::class)
            ->add('action_button_text_color', TextType::class)
            ->add('action_button_background_color', TextType::class)
            ->add('action_button_target_url', TextType::class)
            ->add(
                'action_button_target_page',
                ChoiceType::class,
                array(
                'choices' => $this->retrieveTargetPageList(),
                'placeholder' => 'POINTER SUR'
                )
            )
            ->add(
                'programmed_publication_state',
                ChoiceType::class,
                array(
                'choices' => array(
                    'false' => false,
                    'true' => true,
                ),
                'expanded' => true,
                'multiple' => false,
                )
            )
            ->add(
                'programmed_publication_datetime',
                DateTimeType::class,
                array(
                'label' => false,
                'date_widget' => "single_text",
                'time_widget' => "choice",
                'with_seconds' => false,
                'html5' => false,
                'date_format'=>'dd/MM/yyyy',
                'input' => 'datetime',
                /*'model_timezone' => $this->container->getParameter('app_time_zone'),*/
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => NewsPost::class,
            )
        );
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
                    ->generate('beneficiary_home_pages_standard_slug', array('slug' => $page->getSlug()));
            }
        }

        // put survey and quiz page and questinnaires in options
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
