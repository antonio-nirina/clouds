<?php
namespace AdminBundle\Service\MailChimp;

use DrewM\MailChimp\MailChimp;

class MailChimpHandler
{
    protected $mailchimp;

    public function __construct()
    {
        $this->mailchimp = new MailChimp("564f9f9df4aab20f058693651fbc6e06-us17");
    }
}
