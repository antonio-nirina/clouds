<?php

namespace AdminBundle\Service\MailChimp;

use DrewM\MailChimp\MailChimp;

class MailChimpCampaign
{
    private $mailchimp;

    public function __construct()
    {
        $this->mailchimp = new MailChimp("564f9f9df4aab20f058693651fbc6e06-us17");
    }

    public function getFolders()
    {
        return $this->mailchimp->get("/campaign-folders");
    }

    public function createFolder($name)
    {
        $folders = $this->getFolders();

        $exists = false;
        if ($folders) {
            foreach ($folders["folders"] as $folder) {
                if ($folder['name'] == $name) {
                    $exists = true;
                    break;
                }
            }
        }

        if (!$exists) {
            $this->mailchimp->post("/campaign-folders", ["name" => $name]);
            return array('response'=> true);
        } else {
            return array('error'=> "Ce dossier existe déjà !");
        }
    }
}
