<?php
namespace AdminBundle\Manager;

use AdminBundle\Component\Post\NewsPostSubmissionType;
use AdminBundle\Entity\NewsPost;
use Doctrine\ORM\EntityManager;

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
     * @param $submission_type
     *
     * @return boolean
     */
    public function create(NewsPost $news_post, $submission_type, $flush = true)
    {
        if (!in_array($submission_type, NewsPostSubmissionType::VALID_SUBMISSION_TYPE)) {
            return false;
        }

        $news_post->setProgrammedPublicationState('true' == $news_post->getProgrammedPublicationState());
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
     * Prepare news post data for saving
     *
     * @param NewsPost $news_post
     *
     * @return NewsPost
     */
    public function prepareForSave(NewsPost $news_post)
    {
        $news_post->setPublishedState(false)
            ->setProgrammedPublicationState(false);
        return $news_post;
    }

    /**
     * Prepare news post data for publishing
     *
     * @param NewsPost $news_post
     *
     * @return NewsPost
     */
    public function prepareForPublish(NewsPost $news_post)
    {
        if (false == $news_post->getProgrammedPublicationState()) {
            $news_post->setPublishedState(true);
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
}