<?php

namespace AdminBundle\Service\MailChimp;

use AdminBundle\Service\MailChimp\MailChimpHandler;

class MailChimpCampaign extends MailChimpHandler
{
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
