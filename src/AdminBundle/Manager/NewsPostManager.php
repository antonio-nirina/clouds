<?php
namespace AdminBundle\Manager;

use AdminBundle\Component\Post\NewsPostSubmissionType;
use AdminBundle\Entity\NewsPost;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\Program;

/**
 * Handle news post data manipulation (CRUD)
 */
class NewsPostManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Create news post
     *
     * Create news post
     * Save or Publish depending on submission type
     *
     * @param NewsPost $news_post
     * @param string $submission_type
     * @param boolean $flush
     *
     * @return boolean
     */
    public function create(NewsPost $news_post, $submission_type, $flush = true)
    {
        if (!in_array($submission_type, NewsPostSubmissionType::VALID_SUBMISSION_TYPE)) {
            return false;
        }
//        $news_post->setProgrammedPublicationState('true' == $news_post->getProgrammedPublicationState());
        $news_post = $this->resetPropertiesIfNecessary($news_post);
        if (NewsPostSubmissionType::SAVE == $submission_type) {
            $news_post = $this->prepareForSave($news_post);
        } elseif (NewsPostSubmissionType::PUBLISH == $submission_type) {
            $news_post = $this->prepareForPublish($news_post);
        }
        $home_page_post = $news_post->getHomePagePost();
        $this->em->persist($home_page_post);
        $this->em->persist($news_post);

        if ($flush) {
            $this->flush();
        }

        return true;
    }

    /**
     * Prepare news post data for saving, in post creation
     *
     * @param NewsPost $news_post
     *
     * @return NewsPost
     */
    public function prepareForSave(NewsPost $news_post)
    {
        $news_post->setPublishedState(false)
            ->setProgrammedInProgressState(false);
        return $news_post;
    }

    /**
     * Prepare news post data for publishing, in post creation and edit
     *
     * @param NewsPost $news_post
     *
     * @return NewsPost
     */
    public function prepareForPublish(NewsPost $news_post)
    {
        if (false == $news_post->getProgrammedPublicationState()) {
            $news_post->setPublishedState(true)
                ->setPublicationDatetime(new \DateTime('now'));
        } else {
            $news_post->setProgrammedInProgressState(true);
        }

        return $news_post;
    }

    /**
     * flush on current EntityManager
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * Return all news post related to a program, with specified archived state
     *
     * @param Program $program
     * @param $archived_state
     *
     * @return array
     */
    public function findAll(Program $program, $archived_state)
    {
        return $this->em->getRepository('AdminBundle\Entity\NewsPost')
            ->findAllByProgram($program, $archived_state);
    }

    /**
     * Edit news post
     *
     * Edit news post
     * Save or Publish depending on submission type
     *
     * @param NewsPost $news_post
     * @param string $submission_type
     * @param boolean $flush
     *
     * @return boolean
     */
    public function edit(NewsPost $news_post, $submission_type, $flush = true)
    {
        if (!in_array($submission_type, NewsPostSubmissionType::VALID_SUBMISSION_TYPE)) {
            return false;
        }
//        $news_post->setProgrammedPublicationState('true' == $news_post->getProgrammedPublicationState());
        $news_post = $this->resetPropertiesIfNecessary($news_post);
        if (NewsPostSubmissionType::PUBLISH == $submission_type) {
            $this->prepareForPublish($news_post);
        }
        if ($flush) {
            $this->flush();
        }

        return true;
    }

    /**
     * Reset some news post properties depending on other properties state
     *
     * @param $news_post
     *
     * @return NewsPost
     */
    public function resetPropertiesIfNecessary($news_post)
    {
        // if action button state is false, reset action button properties
        if (false == $news_post->getActionButtonState()) {
            $news_post->setActionButtonText('mon bouton d\'action')
                ->setActionButtonTextColor('#ffffff')
                ->setActionButtonBackgroundColor('#ff0000')
                ->setActionButtonTargetPage(null)
                ->setActionButtonTargetUrl(null);
        }

        // if programmed publication datetime state is false, reset programmed publication datetime
        if (false == $news_post->getProgrammedPublicationState()) {
            $news_post->setProgrammedPublicationDatetime(null);
        }

        return $news_post;
    }
}