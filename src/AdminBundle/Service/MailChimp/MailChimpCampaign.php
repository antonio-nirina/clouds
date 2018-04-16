<?php

namespace AdminBundle\Service\MailChimp;

use AdminBundle\Entity\EmailingCampaign;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MailChimpCampaign extends MailChimpHandler
{
    private $em;

    public function __construct(EntityManager $em)
    {
        parent::__construct();
        $this->em = $em;
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

    public function deleteCampaign($id)
    {
        $success = false;

        while (!$success) {
            $result = $this->mailchimp->delete("/campaigns/{$id}");
            if ($this->mailchimp->success()) {
                $success = true;
            } else {
                dump($this->mailchimp->getLastError());
            }
        }

        return $result;
    }

    public function refreshCampaign()
    {
        $serializer = $this->getSerializer();
        $res = $this->getAllCampaigns();

        foreach ($res["campaigns"] as $campaign) {
            $new = $serializer->deserialize(json_encode($campaign), EmailingCampaign::class, 'json');
            $exists = $this->em->getRepository("AdminBundle:EmailingCampaign")->findById($new->getId());

            if ($exists) {
                $this->em->remove($exists[0]);
            }

            $this->em->persist($new);
        }

        $this->em->flush();

        return $this->em->getRepository("AdminBundle:EmailingCampaign")->findAll();
    }

    public function getSerializer()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        return $serializer = new Serializer($normalizers, $encoders);
    }
}
