<?php
namespace AdminBundle\Service\DataOrdering;

use AdminBundle\Entity\HomePagePost;
use AdminBundle\Component\Post\PostType;
use AdminBundle\Entity\NewsPost;

/**
 * Charge of ordering post
 */
class PostOrdering
{
    /**
     * Order post DESC (older is placed earlier)
     *
     * @param array $postList array of HomePagePost object
     *
     * @return array
     */
    public function orderByDateDesc(array $postList)
    {
        usort(
            $postList,
            function ($a, $b) {
                $aDate = $this->defineToCompareDate($a);
                $bDate = $this->defineToCompareDate($b);
                if ($aDate == $bDate) {
                    return 0;
                }
                return ($aDate > $bDate) ? -1 : 1;
            }
        );

        return $postList;
    }

    /**
     * Define date to compare depending on post type
     *
     * @param HomePagePost $post
     *
     * @return \DateTime
     */
    private function defineToCompareDate(HomePagePost $post)
    {
        if (PostType::NEWS_POST == $post->getPostType()
            || PostType::WELCOMING_NEWS_POST == $post->getPostType()
        ) {
            if (!is_null($post->getNewsPost()) && !is_null($post->getNewsPost()->getPublicationDatetime())) {
                return $post->getNewsPost()->getPublicationDatetime();
            }
        }

        return $post->getCreatedAt();
    }

    /**
     * Order post DESC (older is placed earlier)
     * Depending on post state (waiting, programmed, published)
     *
     * @param array $postList array of NewsPost object
     *
     * @return array
     */
    public function orderNewsPostByMixedDateDesc(array $postList)
    {
        usort(
            $postList,
            function ($a, $b) {
                $aDate = $this->defineToCompareMixedDate($a);
                $bDate = $this->defineToCompareMixedDate($b);
                if ($aDate == $bDate) {
                    return 0;
                }
                return ($aDate > $bDate) ? -1 : 1;
            }
        );

        return $postList;
    }

    /**
     * Define date to compare depending on post state (waiting, programmed, published)
     *
     * @param NewsPost $newsPost
     *
     * @return \DateTime
     */
    public function defineToCompareMixedDate(NewsPost $newsPost)
    {
        $homePagePost = $newsPost->getHomePagePost();
        if (false == $newsPost->getProgrammedInProgressState() && false == $newsPost->getPublishedState()) {
            return $homePagePost->getLastEdit();
        } elseif (false == $newsPost->getPublishedState() && true == $newsPost->getProgrammedInProgressState()) {
            if (!is_null($newsPost->getProgrammedPublicationDatetime())) {
                return $newsPost->getProgrammedPublicationDatetime();
            }
        } elseif (true == $newsPost->getPublishedState()) {
            if (!is_null($newsPost->getPublicationDatetime())) {
                return $newsPost->getPublicationDatetime();
            }
        }

        return $homePagePost->getCreatedAt();
    }
}
