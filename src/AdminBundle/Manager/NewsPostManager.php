<?php
namespace AdminBundle\Manager;

use AdminBundle\Component\Post\NewsPostSubmissionType;
use AdminBundle\Entity\NewsPost;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\Program;
use Gedmo\Tree\Strategy\ORM\Nested;
use AdminBundle\Entity\HomePagePost;

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
     * Prepare news post data for saving, in post edit
     *
     * @param NewsPost $news_post
     *
     * @return NewsPost
     */
    public function prepareEditForSave(NewsPost $news_post)
    {
        if (true == $news_post->getProgrammedInProgressState()
            && false == $news_post->getProgrammedPublicationState()
        ) {
            $news_post->setPublishedState(true)
                ->setProgrammedInProgressState(false);
        }

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
        $news_post = $this->resetPropertiesIfNecessary($news_post);
        if (NewsPostSubmissionType::SAVE == $submission_type) {
            $this->prepareEditForSave($news_post);
        } elseif (NewsPostSubmissionType::PUBLISH == $submission_type) {
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

    /**
     * Duplicate news post
     *
     * @param NewsPost $news_post
     * @param $news_post_title
     * @param bool $flush
     *
     * @return bool
     */
    public function duplicate(NewsPost $news_post, $news_post_title, $flush = true)
    {
        $news_post_copy = $this->generateNewsPostCopyForDuplication($news_post);
        $home_page_post_copy = $this->generateHomePagePostCopyForDuplication(
            $news_post->getHomePagePost(),
            $news_post_copy,
            $news_post_title
        );
        $this->em->persist($news_post_copy);
        $this->em->persist($home_page_post_copy);
        if ($flush) {
            $this->flush();
        }

        return true;
    }

    /**
     * Generate news post copy for duplication
     *
     * Generate news post copy for duplication
     * Should be called before generateHomePagePostCopyForDuplication()
     * to avoid explicit linking between NewsPost and its corresponding HomePagePost
     *
     * @param NewsPost $news_post
     *
     * @return NewsPost
     */
    private function generateNewsPostCopyForDuplication(NewsPost $news_post)
    {
        $news_post_copy = clone $news_post;
        $news_post_copy->setIdToNull()
            ->setHomePagePost(null)
            ->setArchivedState(false)
            ->setPublishedState(false)
            ->setViewNumber(0)
            ->setProgrammedInProgressState(false);

        return $news_post_copy;
    }

    /**
     * Generate home page post for duplication
     *
     * Generate home page post for duplication
     * Should be called after generateNewsPostCopyForDuplication()
     * to avoid explicit linking between NewsPost and its corresponding HomePagePost
     *
     * @param HomePagePost $home_page_post_source
     * @param NewsPost $news_post_copy
     * @param $home_page_post_title
     *
     * @return HomePagePost
     */
    private function generateHomePagePostCopyForDuplication(
        HomePagePost $home_page_post_source,
        NewsPost $news_post_copy,
        $home_page_post_title
    ) {
        $home_page_post_copy = clone $home_page_post_source;
        $home_page_post_copy->setTitle($home_page_post_title)
            ->setIdToNull()
            ->setCreatedAt(null)
            ->setLastEdit(null)
            ->setNewsPost($news_post_copy);
        $news_post_copy->setHomePagePost($home_page_post_copy);

        return $home_page_post_copy;
    }

    /**
     * Publish OR Unpublish news post
     *
     * @param NewsPost $news_post
     * @param $state
     * @param $flush
     */
    public function definePublishedState(NewsPost $news_post, $state, $flush = true)
    {
        $news_post->setPublishedState($state);
        if (true == $state) {
            $news_post->setProgrammedInProgressState(false);
        }

        if ($flush) {
            $this->flush();
        }

        return;
    }

    /**
     * Archive OR Restore news post
     *
     * @param NewsPost $news_post
     * @param boolean $state
     * @param $flush
     */
    public function defineArchivedState(NewsPost $news_post, $state, $flush = true)
    {
        $news_post->setArchivedState(true);

        if ($flush) {
            $this->flush();
        }

        return;
    }
}