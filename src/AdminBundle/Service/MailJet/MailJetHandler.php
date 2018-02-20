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
    const STATUS_CODE_BAD_REQUEST = '400';
    const STATUS_CODE_CONTENT_NOT_CHANGED = '304';
    const ERROR_CODE_EXISTENT_TEMPLATE = 'ps-0015';

    protected $mailjet;

    public function __construct(MailjetClient $mailjet)
    {
        $this->mailjet = $mailjet;
    }
}