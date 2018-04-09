<?php
namespace AdminBundle\Component\GroupAction;

/**
 * Define group action on data
 */
class GroupActionType
{
    const DELETE = 'delete';
    const ARCHIVE = 'archive';
    const RESTORE = 'restore';
    const NEWS_POST_VALID_GROUP_ACTION = array(
        self::DELETE,
        self::ARCHIVE,
        self::RESTORE,
    );
}
