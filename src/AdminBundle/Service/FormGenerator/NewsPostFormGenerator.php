<?php
namespace AdminBundle\Service\FormGenerator;

use AdminBundle\Entity\NewsPost;
use AdminBundle\Entity\HomePagePost;
use AdminBundle\Form\WelcomingNewsPostType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use AdminBundle\Form\NewsPostType;
use AdminBundle\Component\Post\PostType;
use AdminBundle\Entity\Program;
use AdminBundle\Form\DuplicationForm;
use AdminBundle\DTO\DuplicationData;

/**
 * Generate form when manipulating news post (create/edit) with initial underlying objects (for creation)
 */
class NewsPostFormGenerator
{
    private $form_factory;

    public function __construct(FormFactory $form_factory)
    {
        $this->form_factory = $form_factory;
    }

    /**
     * Generate form for news post creation
     *
     * @param Program $program
     * @param string  $post_type takes value from PostType constant
     * @param string  $form_name
     *
     * @return FormInterface
     */
    public function generateForCreation(Program $program, $post_type, $form_name = 'create_news_post_form')
    {
        $home_page_post = $this->initHomePagePostForCreation($program, $post_type);
        $news_post = $this->initNewsPostForCreation($home_page_post);
        if (PostType::WELCOMING_NEWS_POST == $post_type) {
            $form_class = WelcomingNewsPostType::class;
        } else {
            $form_class = NewsPostType::class;
        }

        $form = $this->form_factory->createNamed(
            $form_name,
            $form_class,
            $news_post,
            array('validation_groups' => 'news_post')
        );

        return $form;
    }

    /**
     * Initiate home page post
     *
     * Initiate home page post
     * As home page opst when creation new news post
     *
     * @param Program $program
     * @param string  $post_type
     *
     * @return HomePagePost
     */
    private function initHomePagePostForCreation(Program $program, $post_type)
    {
        $home_page_post = new HomePagePost();
        $home_page_post->setProgram($program)
            ->setPostType($post_type);
        $program->addHomePagePost($home_page_post);

        return $home_page_post;
    }

    /**
     * Initiate news post object
     *
     * Initiate news post object
     * As underlying object when creating new news post
     *
     * @param HomePagePost|null $home_page_post
     *
     * @return NewsPost
     */
    private function initNewsPostForCreation(HomePagePost $home_page_post = null)
    {
        $news_post = new NewsPost();
        if (!is_null($home_page_post)) {
            $news_post->setHomePagePost($home_page_post);
            $home_page_post->setNewsPost($news_post);
        }
        $news_post->setActionButtonState(false)
            ->setActionButtonText('mon bouton d\'action')
            ->setActionButtonTextColor('#ffffff')
            ->setActionButtonBackgroundColor('#ff0000')
            ->setProgrammedPublicationState(false)
            ->setArchivedState(false)
            ->setPublishedState(false)
            ->setViewNumber(0)
            ->setProgrammedInProgressState(false);

        return $news_post;
    }

    /**
     * Generate form for news post edit
     *
     * @param NewsPost $news_post
     * @param string   $post_type
     * @param string   $form_name
     *
     * @return FormInterface
     */
    public function generateForEdit(NewsPost $news_post, $post_type, $form_name = 'edit_news_post_form')
    {
        if (PostType::WELCOMING_NEWS_POST == $post_type) {
            $form_class = WelcomingNewsPostType::class;
        } else {
            $form_class = NewsPostType::class;
        }
        $form = $this->form_factory->createNamed(
            $form_name,
            $form_class,
            $news_post,
            array('validation_groups' => 'news_post')
        );

        return $form;
    }

    /**
     * Generate form for news post duplication
     *
     * @param NewsPost $news_post
     * @param string   $form_name
     *
     * @return FormInterface
     */
    public function generateForDuplication(NewsPost $news_post, $form_name = 'duplicate_news_post_form')
    {
        $duplication_data = new DuplicationData();
        $duplication_data->setName($news_post->getHomePagePost()->getTitle())
            ->setDuplicationSourceId($news_post->getId());
        $form = $this->form_factory->createNamed(
            $form_name,
            DuplicationForm::class,
            $duplication_data
        );

        return $form;
    }
}
