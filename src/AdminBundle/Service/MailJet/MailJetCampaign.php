<?php

namespace AdminBundle\Service\MailJet;

use AdminBundle\Entity\EmailingCampaign;
use Mailjet\MailjetBundle\Client\MailjetClient;
use Mailjet\MailjetBundle\Manager\CampaignDraftManager;
use Mailjet\MailjetBundle\Manager\CampaignManager;
use Mailjet\Resources;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use AdminBundle\Service\MailJet\MailJetHandler;

class MailJetCampaign extends MailJetHandler
{
    const CAMPAIGN_STATUS_ARCHIVED = -1;
    const CAMPAIGN_STATUS_DELETED = -2;
    const CAMPAIGN_STATUS_SENT = 2;
    const CAMPAIGN_NOT_VISIBLE_STATUS_LIST = array(
        self::CAMPAIGN_STATUS_ARCHIVED,
        self::CAMPAIGN_STATUS_DELETED
    );

    protected $campaign_draft_manager;
    protected $campaign_manager;
    /*protected $mailjet;*/

    public function __construct(
        CampaignDraftManager $campaign_draft_manager,
        CampaignManager $campaign_manager,
        MailjetClient $mailjet
    ) {
        parent::__construct($mailjet);
        $this->campaign_draft_manager = $campaign_draft_manager;
        $this->campaign_manager = $campaign_manager;
    }

    public function getAll($data = null, $sort_by_created_at_desc = true)
    {
        $all_campaigns = $this->campaign_draft_manager->getAllCampaignDrafts($data);
        foreach ($all_campaigns as $i => $campaign) {
            $campaign = $this->toObject($campaign);
            $all_campaigns[$i] = $campaign;
        }

        if (true == $sort_by_created_at_desc) {
            $all_campaigns = $this->sortByCreatedAtDesc($all_campaigns);
        }

        return $all_campaigns;
    }

    public function getAllWithData($data = null)
    {
        $campaign_draft_list = $this->getAll($data);
        $campaing_overview_list = $this->getAllCampaignOverview();
        $sent_campaign_list = $this->getAllSentCampaign();

        $campaign_data_list = array();
        foreach ($campaign_draft_list as $key => $campaign_draft) {
            $campaign_data_el['campaign_draft_data'] = $campaign_draft;
            $campaign_data_el['campaign_overview_data'] = null;
            if (self::CAMPAIGN_STATUS_SENT == $campaign_draft->getStatus()) {
                $sent_campaign_id = null;
                foreach ($sent_campaign_list as $sent_campaign) {
                    if ($campaign_draft->getid() == $sent_campaign['NewsLetterID']) {
                        $sent_campaign_id = $sent_campaign['ID'];
                    }
                }

                if (!is_null($sent_campaign_id)) {
                    foreach ($campaing_overview_list as $campaign_overview) {
                        if ($campaign_overview['ID'] == $sent_campaign_id) {
                            $campaign_data_el['campaign_overview_data'] = $campaign_overview;
                        }
                    }
                }
            }

            /*$distant_campaign_id = $campaign->getId();
            foreach ($campaing_overview_list as $campaign_overview) {
                if ($distant_campaign_id == $campaign_overview['ID']) {
                    $campaign_data_el['campaign_overview_data'] = $campaign_overview;
                }
            }*/
            array_push($campaign_data_list, $campaign_data_el);
        }

        return $campaign_data_list;
    }

    public function getAllVisibleWithData($data = null)
    {
        $campaign_data_list = $this->getAllWithData($data);
        foreach ($campaign_data_list as $key => $campaign_data) {
            if (in_array($campaign_data['campaign_draft_data']->getStatus(), self::CAMPAIGN_NOT_VISIBLE_STATUS_LIST)) {
                unset($campaign_data_list[$key]);
            }
        }

        return array_values($campaign_data_list);
    }

    public function getAllCampaignOverview()
    {
        $filters = array('Limit' => 0);
        $response = $this->mailjet->get(Resources::$Campaignoverview, array('filters' => $filters));
        if (in_array($response->getStatus(), self::STATUS_CODE_SUCCESS_LIST)) {
            return $response->getData();
        }

        return array();
    }

    public function getAllSentCampaign()
    {
        $filters = array('FromTS' => 1, 'Limit' => 0);
        $sent_campaign_list = $this->campaign_manager->getAllCampaigns($filters);

        return $sent_campaign_list;
    }

    private function sortByCreatedAtDesc($campaign_list)
    {
        usort($campaign_list, function ($a, $b) {
            $date_a = new \DateTime($a->getCreatedAt());
            $date_b = new \DateTime($b->getCreatedAt());
            if ($date_a == $date_b) {
                return 0;
            }
            return ($date_a > $date_b) ? -1 : 1;
        });

        return $campaign_list;
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
