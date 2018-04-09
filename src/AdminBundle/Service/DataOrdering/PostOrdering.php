<?php
namespace AdminBundle\Service\DataOrdering;

use AdminBundle\Entity\HomePagePost;
use AdminBundle\Component\Post\PostType;

/**
 * Charge of ordering post
 */
class PostOrdering
{
    /**
     * Order post DESC (older is placed earlier)
     *
     * @param array $post_list array of HomePagePost object
     *
     * @return array
     */
    public function orderByDateDesc(array $post_list)
    {
        usort(
            $post_list,
            function ($a, $b) {
                $a_date = $this->defineToCompareDate($a);
                $b_date = $this->defineToCompareDate($b);
                if ($a_date == $b_date) {
                    return 0;
                }
                return ($a_date > $b_date) ? -1 : 1;
            }
        );

        return $post_list;
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
}
