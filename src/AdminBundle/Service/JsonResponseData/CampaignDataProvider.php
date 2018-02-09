<?php
namespace AdminBundle\Service\JsonResponseData;

use AdminBundle\Service\JsonResponseData\StandardDataProvider;

/**
 * Data provider in Campaign manipulation
 * {@inheritdoc}
 */
class CampaignDataProvider extends StandardDataProvider
{
    const CAMPAIGN_SENDING_ERROR = 'Erreur d\'envoi de campagne';

    /**
     * Give data when there is/are error(s) when sending campaign
     *
     * @return array
     */
    public function campaignSendingError()
    {
        return array('error' => self::CAMPAIGN_SENDING_ERROR);
    }
}