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
use Mailjet\MailjetBundle\Model\CampaignDraft;

class MailJetCampaign extends MailJetHandler
{
    const CAMPAIGN_STATUS_ARCHIVED = -1;
    const CAMPAIGN_STATUS_DELETED = -2;
    const CAMPAIGN_STATUS_SENT = 2;
    const CAMPAIGN_STATUS_DRAFT = 0;
    const CAMPAIGN_NOT_VISIBLE_STATUS_LIST = array(
        self::CAMPAIGN_STATUS_ARCHIVED,
        self::CAMPAIGN_STATUS_DELETED
    );
    const CAMPAIGN_FILTER_RECENT = 'Recent';
    const CAMPAIGN_FILTER_SENT = 'Sent';
    const CAMPAIGN_FILTER_PROGRAMMED = 'Programmed';
    const CAMPAIGN_FILTER_DRAFT = 'Draft';
    const CAMPAIGN_VALID_FILTERS = array(
        self::CAMPAIGN_FILTER_RECENT,
        self::CAMPAIGN_FILTER_SENT,
        self::CAMPAIGN_FILTER_PROGRAMMED,
        self::CAMPAIGN_FILTER_DRAFT,
    );

    const CAMPAIGN_ARCHIVED_VALID_FILTERS = array(
        self::CAMPAIGN_FILTER_RECENT,
        self::CAMPAIGN_FILTER_SENT,
        self::CAMPAIGN_FILTER_DRAFT,
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
                    if ($campaign_draft->getId() == $sent_campaign['NewsLetterID']) {
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

    public function getAllArchivedWithData($data = null)
    {
        $archived_filter = array('Status' => self::CAMPAIGN_STATUS_ARCHIVED);
        $data = is_null($data) ? $archived_filter : array_merge($data, $archived_filter);
        $campaign_data_list = $this->getAllWithData($data);

        return $campaign_data_list;
    }

    public function getAllArchivedWithDataFiltered($filter_value)
    {
        $archived_campaign_with_data = $this->getAllArchivedWithData(array('Limit' => 0));
        if (is_null($filter_value)) {
            return $archived_campaign_with_data;
        }

        if (self::CAMPAIGN_FILTER_RECENT == $filter_value) {
            return array_slice($archived_campaign_with_data, 0, 10);
        } elseif (self::CAMPAIGN_FILTER_SENT == $filter_value) {
            $filtered_archived_campaign_with_data = array();
            foreach ($archived_campaign_with_data as $campaign_data) {
                if ('' != $campaign_data['campaign_draft_data']->getDeliveredAt()) {
                    array_push($filtered_archived_campaign_with_data, $campaign_data);
                }
            }
            return $filtered_archived_campaign_with_data;
        } elseif (self::CAMPAIGN_FILTER_DRAFT == $filter_value) {
            $filtered_archived_campaign_with_data = array();
            foreach ($archived_campaign_with_data as $campaign_data) {
                if ('' == $campaign_data['campaign_draft_data']->getDeliveredAt()) {
                    array_push($filtered_archived_campaign_with_data, $campaign_data);
                }
            }
            return $filtered_archived_campaign_with_data;
        }

        /*if (self::CAMPAIGN_FILTER_RECENT == $filter_value) {
            return array_slice($this->getAllArchivedWithData(array('Limit' => 0)), 0, 10);
        } elseif (self::CAMPAIGN_FILTER_SENT == $filter_value) {
            if (in_array($filter_value, self::CAMPAIGN_ARCHIVED_VALID_FILTERS)) {
                return $this->getAllArchivedWithData(array('Limit' => 0, 'Status' => $filter_value));
            }
        }*/

        return array();
    }

    public function getAllVisibleWithDataFiltered($filter_value)
    {
        if (is_null($filter_value)) {
            return $this->getAllVisibleWithData(array('Limit' => 0));
        }

        if (self::CAMPAIGN_FILTER_RECENT == $filter_value) {
            return array_slice($this->getAllVisibleWithData(array('Limit' => 0)), 0, 10);
        } else {
            if (in_array($filter_value, self::CAMPAIGN_VALID_FILTERS)) {
                return $this->getAllVisibleWithData(array('Limit' => 0, 'Status' => $filter_value));
            }
        }
        return array();
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

    /*public function getCampaignId($campaign)
    {
        $id = $campaign->getId();
    }*/

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

    public function archiveCampaignDraftByIdList(array $campaign_draft_id_list)
    {
        if (!empty($campaign_draft_id_list)) {
            foreach ($campaign_draft_id_list as $campaign_draft_id) {
                $this->mailjet->put(Resources::$Campaigndraft, array(
                    'Id' => $campaign_draft_id,
                    'body' => array('Status' => self::CAMPAIGN_STATUS_ARCHIVED),
                ));
            }
        }

        return;
    }

    public function restoreArchivedCampaignDraftByIdList(array $campaign_draft_id_list)
    {
        if (!empty($campaign_draft_id_list)) {
            foreach ($campaign_draft_id_list as $campaign_draft_id) {
                $result = $this->mailjet->get(Resources::$Campaigndraft, array('Id' => $campaign_draft_id));
                if (self::STATUS_CODE_SUCCESS == $result->getStatus()) {
                    $campaign_draft = $result->getData()[0];
                    if ('' == $campaign_draft['DeliveredAt']) {
                        $body = array('Status' => self::CAMPAIGN_STATUS_DRAFT);
                    } else {
                        $body = array('Status' => self::CAMPAIGN_STATUS_SENT);
                    }
                    $this->mailjet->put(Resources::$Campaigndraft, array(
                        'Id' => $campaign_draft_id,
                        'body' => $body,
                    ));
                }
            }
        }

        return;
    }

    /**
     * Retrieve campaign draft data by its id
     *
     * @param int $id
     *
     * @return null|array
     */
    public function retrieveCampaignDraftById($id)
    {
        $result = $this->mailjet->get(Resources::$Campaigndraft, array('Id' => $id));
        if (self::STATUS_CODE_SUCCESS == $result->getStatus()) {
            return $result->getData()[0];
        }

        return null;
    }


    /**
     * Duplicate campaign draft
     *
     * @param array $source_campaign_draft_data
     * @param string $campaign_draft_title
     *
     * @return null|int
     */
    public function duplicateCampaignDraft(array $source_campaign_draft_data, $campaign_draft_title)
    {
        unset($source_campaign_draft_data['CreatedAt']);
        unset($source_campaign_draft_data['Current']);
        unset($source_campaign_draft_data['ID']);
        unset($source_campaign_draft_data['ModifiedAt']);
        unset($source_campaign_draft_data['DeliveredAt']);
        $source_campaign_draft_data['Status'] = 0;
        $source_campaign_draft_data['Title'] = $campaign_draft_title;
        $result = $this->mailjet->post(Resources::$Campaigndraft, array('body' => $source_campaign_draft_data));
        if (in_array($result->getStatus(), self::STATUS_CODE_SUCCESS_LIST)) {
            return $result->getData()[0]['ID'];
        }

        return null;
    }

    /**
     * Delete campaign draft by ID list
     *
     * @param array $campaign_draft_id_list
     *
     * @return void
     */
    public function deleteCampaignDraftByIdList(array $campaign_draft_id_list)
    {
        if (!empty($campaign_draft_id_list)) {
            foreach ($campaign_draft_id_list as $campaign_draft_id) {
                $this->mailjet->put(Resources::$Campaigndraft, array(
                    'Id' => $campaign_draft_id,
                    'body' => array('Status' => self::CAMPAIGN_STATUS_DELETED),
                ));
            }
        }

        return;
    }
}
