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
use AdminBundle\DTO\CampaignDraftData;
use AdminBundle\Service\MailJet\MailJetSender;
use AdminBundle\Service\MailJet\MailjetContactList;
use AdminBundle\Service\ArrayStructure\ArrayStructureHandler;

class MailJetCampaign extends MailJetHandler
{
    const CAMPAIGN_STATUS_ARCHIVED = -1;
    const CAMPAIGN_STATUS_DELETED = -2;
    const CAMPAIGN_STATUS_SENT = 2;
    const CAMPAIGN_STATUS_DRAFT = 0;
    const CAMPAIGN_STATUS_PROGRAMMED = 1;
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
    const DEFAULT_EDIT_MODE = 'tool2';
    const DEFAULT_LOCALE = 'fr_FR';
    const DEFAULT_SUBJECT = '(Objet)';
    const DEFAULT_TITLE = '(Campagne)';

    protected $campaign_draft_manager;
    protected $campaign_manager;
    protected $mailjet_sender_handler;
    protected $contact_list_handler;
    protected $array_structure_handler;
    /*protected $mailjet;*/

    public function __construct(
        CampaignDraftManager $campaign_draft_manager,
        CampaignManager $campaign_manager,
        MailjetClient $mailjet,
        MailJetSender $mailjet_sender_handler,
        MailjetContactList $contact_list_handler,
        ArrayStructureHandler $array_structure_handler
    ) {
        parent::__construct($mailjet);
        $this->campaign_draft_manager = $campaign_draft_manager;
        $this->campaign_manager = $campaign_manager;
        $this->mailjet_sender_handler = $mailjet_sender_handler;
        $this->contact_list_handler = $contact_list_handler;
        $this->array_structure_handler = $array_structure_handler;
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

    /**
     * Get all campaign draft with associated data
     *
     * @param null $data
     *
     * @return array
     */
    public function getAllWithData($data = null)
    {
        $campaign_draft_list = $this->getAll($data);

        $campaign_overview_list = $this->getAllCampaignOverview();
        $indexed_campaign_overview_list = $this->array_structure_handler->redefineKeysBySpecifiedElementKey(
            'ID',
            $campaign_overview_list
        );

        $sent_campaign_list = $this->getAllSentCampaign();
        $indexed_sent_campaign_list = $this->array_structure_handler->redefineKeysBySpecifiedElementKey(
            'NewsLetterID',
            $sent_campaign_list
        );

        $contact_list_list =  $this->contact_list_handler->getAllList();
        $indexed_contact_list_list = $this->array_structure_handler->redefineKeysBySpecifiedElementKey(
            'ID',
            $contact_list_list
        );

        $campaign_data_list = array();
        foreach ($campaign_draft_list as $key => $campaign_draft) {
            // setting campaign draft data
            $campaign_data_el['campaign_draft_data'] = $campaign_draft;

            // setting campaign overview associated data, for sent campaign only
            $campaign_data_el['campaign_overview_data'] = null;
            if (self::CAMPAIGN_STATUS_SENT == $campaign_draft->getStatus()) {
                $sent_campaign_id = null;
                if (array_key_exists($campaign_draft->getId(), $indexed_sent_campaign_list)) {
                    $sent_campaign_id = $indexed_sent_campaign_list[$campaign_draft->getId()]['ID'];
                }
                if (!is_null($sent_campaign_id)) {
                    if (array_key_exists($sent_campaign_id, $indexed_campaign_overview_list)) {
                        $campaign_data_el['campaign_overview_data'] =
                            $indexed_campaign_overview_list[$sent_campaign_id];
                    }
                }
            }

            // setting contact list associated data, when campaign draft contact list is defined
            $campaign_data_el['contact_list_data'] = null;
            if (!is_null($campaign_draft->getContactsListId())) {
                if (array_key_exists($campaign_draft->getContactsListId(), $indexed_contact_list_list)) {
                    $campaign_data_el['contact_list_data'] =
                        $indexed_contact_list_list[$campaign_draft->getContactsListId()];
                }
            }

            // adding element (which is an array) to array
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
     * Duplicate campaign draft data
     * And create campaign draft content from source content data
     *
     * @param array $source_campaign_draft_data
     * @param string $campaign_draft_title
     *
     * @return null|int
     */
    public function duplicateCampaignDraft(array $source_campaign_draft_data, $campaign_draft_title)
    {
        $source_campaign_draft_id = $source_campaign_draft_data['ID'];
        unset($source_campaign_draft_data['CreatedAt']);
        unset($source_campaign_draft_data['Current']);
        unset($source_campaign_draft_data['ID']);
        unset($source_campaign_draft_data['ModifiedAt']);
        unset($source_campaign_draft_data['DeliveredAt']);
        unset($source_campaign_draft_data['Url']);
        $source_campaign_draft_data['IsStarred'] = false;
        $source_campaign_draft_data['Status'] = 0;
        $source_campaign_draft_data['Title'] = $campaign_draft_title;
        $source_campaign_draft_data['Used'] = false;
        $result = $this->mailjet->post(Resources::$Campaigndraft, array('body' => $source_campaign_draft_data));
        if (in_array($result->getStatus(), self::STATUS_CODE_SUCCESS_LIST)) {
            $result_source_campaign_draft_content = $this->mailjet->get(Resources::$CampaigndraftDetailcontent, array(
                'id' => $source_campaign_draft_id,
            ));
            if (self::STATUS_CODE_SUCCESS == $result_source_campaign_draft_content->getStatus()) {
                $source_campaign_draft_content = $result_source_campaign_draft_content->getData()[0];
                $content_body = array(
                    'Text-part' => $source_campaign_draft_content['Text-part'],
                    'Html-part' => $source_campaign_draft_content['Html-part'],
                );
                $result_create_content = $this->mailjet->post(Resources::$CampaigndraftDetailcontent, array(
                    'id' => $result->getData()[0]['ID'],
                    'body' => $content_body,
                ));
                if (self::STATUS_CODE_CREATED == $result_create_content->getStatus()) {
                    return $result->getData()[0]['ID'];
                } else {
                    $this->deleteCampaignDraftByIdList(array($result->getData()[0]['ID']));
                }

            } else {
                $this->deleteCampaignDraftByIdList(array($result->getData()[0]['ID']));
            }
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

    /**
     * Create campaign data. Then send or program it.
     *
     *  @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function createAndProcess(CampaignDraftData $campaign_draft_data)
    {
        if ('true' == $campaign_draft_data->getProgrammedState()) {
            return $this->createAndProgram($campaign_draft_data);
        } else {
            return $this->createAndSend($campaign_draft_data);
        }
    }

    /**
     * Create campaign draft and set its content
     *
     * @param CampaignDraftData $campaign_draft_data
     * @param int $status
     *
     * @return null|int
     */
    private function createCampaignDraft(CampaignDraftData $campaign_draft_data, $status = 0)
    {
        $sender = $this->mailjet_sender_handler->getDefault();
        if (!is_null($sender)) {
            $body = array(
                'ContactsListID' => $campaign_draft_data->getListId(),
                'EditMode' => self::DEFAULT_EDIT_MODE,
                'Locale' => self::DEFAULT_LOCALE,
                'Sender' => $sender['ID'],
                'SenderEmail' => $sender['Email'],
                'Status' => $status,
                'Subject' => $campaign_draft_data->getSubject(),
                'TemplateID' => $campaign_draft_data->getTemplateId(),
                'Title' => $campaign_draft_data->getName()
            );
            $create_result = $this->mailjet->post(Resources::$Campaigndraft, array('body' => $body));
            if (self::STATUS_CODE_CREATED == $create_result->getStatus()) {
                $template_content_result = $this
                    ->mailjet
                    ->get(Resources::$TemplateDetailcontent, array('id' => $campaign_draft_data->getTemplateId()));
                if (self::STATUS_CODE_SUCCESS == $template_content_result->getStatus()) {
                    $content_body = array(
                        'Text-part' => $template_content_result->getData()[0]['Text-part'],
                        'Html-part' => $template_content_result->getData()[0]['Html-part'],
                    );
                    $set_content_result = $this
                        ->mailjet
                        ->post(
                            Resources::$CampaigndraftDetailcontent,
                            array('id' => $create_result->getData()[0]['ID'], 'body' => $content_body)
                        );
                    if (self::STATUS_CODE_CREATED == $set_content_result->getStatus()) {
                        return $create_result->getData()[0]['ID'];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Create campaign draft then send it
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function createAndSend(CampaignDraftData $campaign_draft_data)
    {
        $campaign_draft_id = $this->createCampaignDraft($campaign_draft_data);
        if (!is_null($campaign_draft_id)) {
            $send_result = $this
                ->mailjet
                ->post(Resources::$CampaigndraftSend, array('id' => $campaign_draft_id));
            if (self::STATUS_CODE_CREATED == $send_result->getStatus()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create campaign draft then program it
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function createAndProgram(CampaignDraftData $campaign_draft_data)
    {
        $campaign_draft_id = $this->createCampaignDraft($campaign_draft_data, self::CAMPAIGN_STATUS_PROGRAMMED);
        if (!is_null($campaign_draft_id)) {
            $schedule_body = array(
                'Date' => $campaign_draft_data->getProgrammedLaunchDate()->format(\DateTime::RFC3339)
            );
            $set_schedule_result = $this->mailjet->put(Resources::$CampaigndraftSchedule, array(
                'id' => $campaign_draft_id,
                'body' => $schedule_body
            ));
            if (self::STATUS_CODE_SUCCESS == $set_schedule_result->getStatus()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create campaign draft when halting campaign creation
     *
     * Create campaign draft when halting campaign creation
     * Use provided datas, or use default datas when these formers are not yet defined/are still null
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return null|int
     */
    public function createCampaignDraftByHalt(CampaignDraftData $campaign_draft_data)
    {
        $sender = $this->mailjet_sender_handler->getDefault();
        if (!is_null($sender)) {
            $body = array(
                'EditMode' => self::DEFAULT_EDIT_MODE,
                'Locale' => self::DEFAULT_LOCALE,
                'Sender' => $sender['ID'],
                'SenderEmail' => $sender['Email'],
                'Status' => self::CAMPAIGN_STATUS_DRAFT,
                'Subject' => self::DEFAULT_SUBJECT,
                'Title' => self::DEFAULT_TITLE,
            );
            if (!is_null($campaign_draft_data->getListId())) {
                $body['ContactsListID'] = $campaign_draft_data->getListId();
            }
            if (!is_null($campaign_draft_data->getSubject())) {
                $body['Subject'] = $campaign_draft_data->getSubject();
            }
            if (!is_null($campaign_draft_data->getName())) {
                $body['Title'] = $campaign_draft_data->getName();
            }
            if (!is_null($campaign_draft_data->getTemplateId())) {
                $body['TemplateID'] = $campaign_draft_data->getTemplateId();
            }
            $create_result = $this->mailjet->post(Resources::$Campaigndraft, array('body' => $body));
            if (self::STATUS_CODE_CREATED == $create_result->getStatus()) {
                if (!is_null($campaign_draft_data->getTemplateId())) {
                    $template_content_result = $this
                        ->mailjet
                        ->get(Resources::$TemplateDetailcontent, array('id' => $campaign_draft_data->getTemplateId()));
                    if (self::STATUS_CODE_SUCCESS == $template_content_result->getStatus()) {
                        $content_body = array(
                            'Text-part' => $template_content_result->getData()[0]['Text-part'],
                            'Html-part' => $template_content_result->getData()[0]['Html-part'],
                        );
                        $set_content_result = $this
                            ->mailjet
                            ->post(
                                Resources::$CampaigndraftDetailcontent,
                                array('id' => $create_result->getData()[0]['ID'], 'body' => $content_body)
                            );
                        if (self::STATUS_CODE_CREATED == $set_content_result->getStatus()) {
                            return $create_result->getData()[0]['ID'];
                        }
                    }
                } else {
                    return $create_result->getData()[0]['ID'];
                }
            }
        }


        return null;
    }

    /**
     * Find campaign draft data by id
     *
     * @param $campaign_draft_id
     *
     * @return null|CampaignDraftData|null
     */
    public function findCampaignDraftAsDTO($campaign_draft_id)
    {
        $result = $this->mailjet->get(Resources::$Campaigndraft, array('id' => $campaign_draft_id));
        if (self::STATUS_CODE_SUCCESS == $result->getStatus()) {
            $campaign_draft_data = new CampaignDraftData();
            $campaign_draft_data->setId($result->getData()[0]['ID'])
                ->setName($result->getData()[0]['Title'])
                ->setSubject($result->getData()[0]['Subject'])
                ->setListId(
                    array_key_exists('ContactsListID', $result->getData()[0])
                    ? $result->getData()[0]['ContactsListID']
                    : null
                )
                ->setTemplateId(
                    array_key_exists('TemplateID', $result->getData()[0])
                    ? $result->getData()[0]['TemplateID']
                    : null
                )
                ->setProgrammedState(self::CAMPAIGN_STATUS_PROGRAMMED == $result->getData()[0]['Status']);
            if (self::CAMPAIGN_STATUS_PROGRAMMED == $result->getData()[0]['Status']) {
                $result_schedule = $this->mailjet
                    ->get(Resources::$CampaigndraftSchedule, array('id' => $campaign_draft_id));
                if (self::STATUS_CODE_SUCCESS == $result_schedule->getStatus()) {
                    $campaign_draft_data
                        ->setProgrammedLaunchDate(new \DateTime($result_schedule->getData()[0]['Date']));
                } else {
                    return null;
                }
            }
            return $campaign_draft_data;
        }

        return null;
    }

    /**
     * Edit campaign data. Then send or program it.
     *
     *  @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function editAndProcess(CampaignDraftData $campaign_draft_data)
    {
        if ('true' == $campaign_draft_data->getProgrammedState()) {
            return $this->editAndProgram($campaign_draft_data);
        } else {
            return $this->editAndSend($campaign_draft_data);
        }
    }

    /**
     * Edit campaign draft then send it
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function editAndSend(CampaignDraftData $campaign_draft_data)
    {
        if ($this->editCampaignDraft($campaign_draft_data) && !is_null($campaign_draft_data->getId())) {
            $send_result = $this
                ->mailjet
                ->post(Resources::$CampaigndraftSend, array('id' => $campaign_draft_data->getId()));
            if (self::STATUS_CODE_CREATED == $send_result->getStatus()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Edit campaign draft then program it
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function editAndProgram(CampaignDraftData $campaign_draft_data)
    {
        if ($this->editCampaignDraft($campaign_draft_data) && !is_null($campaign_draft_data->getId())) {
            $schedule_body = array(
                'Date' => $campaign_draft_data->getProgrammedLaunchDate()->format(\DateTime::RFC3339)
            );
            $set_schedule_result = $this->mailjet->put(Resources::$CampaigndraftSchedule, array(
                'id' => $campaign_draft_data->getId(),
                'body' => $schedule_body
            ));
            if (self::STATUS_CODE_SUCCESS == $set_schedule_result->getStatus()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Edit campaign draft and update its content
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function editCampaignDraft(CampaignDraftData $campaign_draft_data)
    {
        $sender = $this->mailjet_sender_handler->getDefault();
        if (!is_null($sender)) {
            $body = array(
                'ContactsListID' => $campaign_draft_data->getListId(),
                'Subject' => $campaign_draft_data->getSubject(),
                'TemplateID' => $campaign_draft_data->getTemplateId(),
                'Title' => $campaign_draft_data->getName(),
                'Locale' => self::DEFAULT_LOCALE,
                'Sender' => $sender['ID'],
                'SenderEmail' => $sender['Email'],
            );
            $edit_result = $this->mailjet->put(Resources::$Campaigndraft, array(
                'id' => $campaign_draft_data->getId(),
                'body' => $body
            ));
            if (self::STATUS_CODE_SUCCESS == $edit_result->getStatus()
                || self::STATUS_CODE_CONTENT_NOT_CHANGED == $edit_result->getStatus()
            ) {
                $template_content_result = $this
                    ->mailjet
                    ->get(Resources::$TemplateDetailcontent, array('id' => $campaign_draft_data->getTemplateId()));
                if (self::STATUS_CODE_SUCCESS == $template_content_result->getStatus()) {
                    $content_body = array(
                        'Text-part' => $template_content_result->getData()[0]['Text-part'],
                        'Html-part' => $template_content_result->getData()[0]['Html-part'],
                    );
                    $set_content_result = $this
                        ->mailjet
                        ->post(
                            Resources::$CampaigndraftDetailcontent,
                            array('id' => $campaign_draft_data->getId(), 'body' => $content_body)
                        );
                    if (self::STATUS_CODE_CREATED == $set_content_result->getStatus()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Save campaign draft when halting campaign edit
     *
     * @param CampaignDraftData $campaign_draft_data
     *
     * @return bool
     */
    public function editCampaignDraftByHalt(CampaignDraftData $campaign_draft_data)
    {
        $sender = $this->mailjet_sender_handler->getDefault();
        if (!is_null($sender)) {
            $body = array(
                'Locale' => self::DEFAULT_LOCALE,
                'Sender' => $sender['ID'],
                'SenderEmail' => $sender['Email'],
            );
            if (!is_null($campaign_draft_data->getListId())) {
                $body['ContactsListID'] = $campaign_draft_data->getListId();
            }
            if (!is_null($campaign_draft_data->getSubject())) {
                $body['Subject'] = $campaign_draft_data->getSubject();
            }
            if (!is_null($campaign_draft_data->getName())) {
                $body['Title'] = $campaign_draft_data->getName();
            }
            if (!is_null($campaign_draft_data->getTemplateId())) {
                $body['TemplateID'] = $campaign_draft_data->getTemplateId();
            }
            $edit_result = $this->mailjet->put(Resources::$Campaigndraft, array(
                'id' => $campaign_draft_data->getId(),
                'body' => $body
            ));
            if (self::STATUS_CODE_SUCCESS == $edit_result->getStatus()
                || self::STATUS_CODE_CONTENT_NOT_CHANGED == $edit_result->getStatus()
            ) {
                $template_content_result = $this
                    ->mailjet
                    ->get(Resources::$TemplateDetailcontent, array('id' => $campaign_draft_data->getTemplateId()));
                if (self::STATUS_CODE_SUCCESS == $template_content_result->getStatus()) {
                    $content_body = array(
                        'Text-part' => $template_content_result->getData()[0]['Text-part'],
                        'Html-part' => $template_content_result->getData()[0]['Html-part'],
                    );
                    $set_content_result = $this
                        ->mailjet
                        ->post(
                            Resources::$CampaigndraftDetailcontent,
                            array('id' => $campaign_draft_data->getId(), 'body' => $content_body)
                        );
                    if (self::STATUS_CODE_CREATED == $set_content_result->getStatus()) {
                        return true;
                    }
                }
            }
        }
    }
}
