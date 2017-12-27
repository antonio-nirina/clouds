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
            return array('response'=> $this->mailchimp->post("/campaign-folders", ["name" => $name]));
        } else {
            return array('error'=> "Ce dossier existe déjà !");
        }
    }

    public function getAllCampaigns($data = array())
    {
        return $this->mailchimp->get("/campaigns", $data);
    }

    public function replicateCampaign($id)
    {
        return $this->mailchimp->post("/campaigns/{$id}/actions/replicate");
    }

    public function renameCampaign($id, $name)
    {
        return $this->mailchimp->patch("/campaigns/{$id}", ["settings" => ["title" => $name]]);
    }
}
