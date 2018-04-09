<?php
namespace AdminBundle\Service\DataLinker;

use AdminBundle\Component\Post\NewsPostTypeLabel;
use AdminBundle\Component\Post\PostType;

/**
 * data linker for news post data
 */
class NewsPostDataLinker
{
    /**
     * give post type corresponding to given type label
     *
     * @param $type_label
     *
     * @return null|string
     */
    public function linkTypeLabelToType($type_label)
    {
        $post_type = null;
        switch ($type_label) {
            case NewsPostTypeLabel::STANDARD:
                $post_type = PostType::NEWS_POST;
                break;
            case NewsPostTypeLabel::WELCOMING:
                $post_type = PostType::WELCOMING_NEWS_POST;
                break;
        }

        return $post_type;
    }
}
