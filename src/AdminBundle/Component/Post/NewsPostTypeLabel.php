<?php
namespace AdminBundle\Component\Post;

/**
 * Holding news post type label (standard OR welcoming). Used in routing
 */
class NewsPostTypeLabel
{
    const STANDARD = 'standard';
    const WELCOMING = 'publications-accueil';
    const VALID_NEWS_POST_TYPE_LABEL = array(
        self::STANDARD,
        self::WELCOMING,
    );
}
