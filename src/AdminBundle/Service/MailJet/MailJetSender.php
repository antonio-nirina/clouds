<?php
namespace AdminBundle\Service\MailJet;

use AdminBundle\Service\MailJet\MailJetHandler;
use Mailjet\Resources;

/**
 * Service for manipulating MailJet Sender datas
 */
class MailJetSender extends MailJetHandler
{
    /**
     * Retrieve default sender (first in result)
     *
     * @return null|array
     */
    public function getDefault()
    {
        $result = $this->mailjet->get(Resources::$Sender);
        if (self::STATUS_CODE_SUCCESS == $result->getStatus()) {
            return $result->getData()[0];
        }

        return null;
    }
}
