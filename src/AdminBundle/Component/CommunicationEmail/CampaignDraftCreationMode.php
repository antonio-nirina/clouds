<?php
namespace AdminBundle\Component\CommunicationEmail;

/**
 * Define campaign draft creation mode as constant
 */
class CampaignDraftCreationMode
{
    /**
     * Used when campaign draft is created following all steps
     */
    const NORMAL = 'normal-mode';

    /**
     * Used when campaign draft is created after suspending creation
     */
    const BY_HALT = 'by-halt-mode';

    /**
     * Valid mode when creating campaign draft
     */
    const VALID_CREATION_MODE = array(
        self::NORMAL,
        self::BY_HALT,
    );
}