<?php
namespace AdminBundle\Component\Post;

/**
 * Post submission
 *
 * Post authorization, for eg SAVE or PUBLISH
 */

class NewsPostSubmissionType
{
    const SAVE = 'submission-type-save';
    const PUBLISH = 'submission-type-publish';
    const VALID_SUBMISSION_TYPE = array(
        self::SAVE,
        self::PUBLISH,
    );
}