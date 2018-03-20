<?php
namespace AdminBundle\Component\Submission;

/**
 * Data submission type
 *
 * Data submission type, for eg SAVE or PUBLISH
 */
class SubmissionType
{
    const SAVE = 'submission-type-save';
    const PUBLISH = 'submission-type-publish';
    const VALID_SUBMISSION_TYPE = array(
        self::SAVE,
        self::PUBLISH,
    );
}