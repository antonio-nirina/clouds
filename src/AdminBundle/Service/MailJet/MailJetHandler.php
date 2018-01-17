<?php
namespace AdminBundle\Service\MailJet;

use Mailjet\MailjetBundle\Client\MailjetClient;

class MailJetHandler
{
    const STATUS_CODE_SUCCESS = '200';
    const STATUS_CODE_CREATED = '201';
    const STATUS_CODE_SUCCESS_LIST = array('200', '201');
    const STATUS_CODE_NO_CONTENT = '204';
    const STATUS_CODE_NOT_FOUND = '404';

    protected $mailjet;

    public function __construct(MailjetClient $mailjet)
    {
        $this->mailjet = $mailjet;
    }
}