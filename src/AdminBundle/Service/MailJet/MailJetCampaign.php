<?php

namespace AdminBundle\Service\MailJet;

use AdminBundle\Entity\EmailingCampaign;
use Mailjet\MailjetBundle\Client\MailjetClient;
use Mailjet\MailjetBundle\Manager\CampaignDraftManager;
use Mailjet\Resources;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MailJetCampaign
{
    protected $manager;
    protected $mailjet;

    public function __construct(CampaignDraftManager $manager, MailjetClient $mailjet)
    {
        $this->manager = $manager;
        $this->mailjet = $mailjet;
    }

    public function getAll($data = null)
    {
        $all_campaigns = $this->manager->getAllCampaignDrafts($data);

        foreach ($all_campaigns as $i => $campaign) {
            $campaign = $this->toObject($campaign);
            $all_campaigns[$i] = $campaign;
        }

        return $all_campaigns;
    }

    public function toObject($campaign)
    {
        return $this->getSerializer()->deserialize(json_encode($campaign), EmailingCampaign::class, 'json');
    }

    public function getCampaignId($campaign)
    {
        $id = $campaign->getId();
    }

    public function getSerializer()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        return $serializer = new Serializer($normalizers, $encoders);
    }

    public function getStatistics()
    {
        $response = $this->mailjet->get(Resources::$Campaignstatistics);
        if (!$response->success()) {
            $this->throwError("Echec", $response);
        }

        return $response->getData();
    }
}
