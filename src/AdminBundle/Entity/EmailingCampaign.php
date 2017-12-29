<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\EmailingCampaignRepository")
 * @ORM\Table(name="emailing_campaign")
 */
class EmailingCampaign
{
    /**
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id()
     */
    private $id;

    /**
     * @ORM\Column(name="web_id", type="integer", nullable=false)
     */
    private $web_id;

    /**
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(name="create_time", type="string", nullable=true)
     */
    private $create_time;

    /**
     * @ORM\Column(name="archive_url", type="text", nullable=true)
     */
    private $archive_url;

    /**
     * @ORM\Column(name="long_archive_url", type="text", nullable=true)
     */
    private $long_archive_url;

    /**
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(name="emails_sent", type="integer", nullable=true)
     */
    private $emails_sent;

    /**
     * @ORM\Column(name="send_time", type="string", nullable=true)
     */
    private $send_time;

    /**
     * @ORM\Column(name="content_type", type="string", nullable=true)
     */
    private $content_type;

    /**
     * @ORM\Column(name="needs_block_refresh", type="boolean", nullable=true)
     */
    private $needs_block_refresh;

    /**
     * @ORM\Column(name="recipients", type="array", nullable=true)
     */
    private $recipients;

    /**
     * @ORM\Column(name="settings", type="array", nullable=true)
     */
    private $settings;

    /**
     * @ORM\Column(name="variate_settings", type="array", nullable=true)
     */
    private $variate_settings;

    /**
     * @ORM\Column(name="tracking", type="array", nullable=true)
     */
    private $tracking;

    /**
     * @ORM\Column(name="rss_opts", type="array", nullable=true)
     */
    private $rss_opts;

    /**
     * @ORM\Column(name="ab_split_opts", type="array", nullable=true)
     */
    private $ab_split_opts;

    /**
     * @ORM\Column(name="social_card", type="array", nullable=true)
     */
    private $social_card;

    /**
     * @ORM\Column(name="report_summary", type="array", nullable=true)
     */
    private $report_summary;

    /**
     * Get localId
     *
     * @return integer
     */
    public function getLocalId()
    {
        return $this->local_id;
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return EmailingCampaign
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set webId
     *
     * @param integer $webId
     *
     * @return EmailingCampaign
     */
    public function setWebId($webId)
    {
        $this->web_id = $webId;

        return $this;
    }

    /**
     * Get webId
     *
     * @return integer
     */
    public function getWebId()
    {
        return $this->web_id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return EmailingCampaign
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     *
     * @return EmailingCampaign
     */
    public function setCreateTime($createTime)
    {
        $this->create_time = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * Set archiveUrl
     *
     * @param string $archiveUrl
     *
     * @return EmailingCampaign
     */
    public function setArchiveUrl($archiveUrl)
    {
        $this->archive_url = $archiveUrl;

        return $this;
    }

    /**
     * Get archiveUrl
     *
     * @return string
     */
    public function getArchiveUrl()
    {
        return $this->archive_url;
    }

    /**
     * Set longArchiveUrl
     *
     * @param string $longArchiveUrl
     *
     * @return EmailingCampaign
     */
    public function setLongArchiveUrl($longArchiveUrl)
    {
        $this->long_archive_url = $longArchiveUrl;

        return $this;
    }

    /**
     * Get longArchiveUrl
     *
     * @return string
     */
    public function getLongArchiveUrl()
    {
        return $this->long_archive_url;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return EmailingCampaign
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set emailsSent
     *
     * @param integer $emailsSent
     *
     * @return EmailingCampaign
     */
    public function setEmailsSent($emailsSent)
    {
        $this->emails_sent = $emailsSent;

        return $this;
    }

    /**
     * Get emailsSent
     *
     * @return integer
     */
    public function getEmailsSent()
    {
        return $this->emails_sent;
    }

    /**
     * Set sendTime
     *
     * @param \DateTime $sendTime
     *
     * @return EmailingCampaign
     */
    public function setSendTime($sendTime)
    {
        $this->send_time = $sendTime;

        return $this;
    }

    /**
     * Get sendTime
     *
     * @return \DateTime
     */
    public function getSendTime()
    {
        return $this->send_time;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     *
     * @return EmailingCampaign
     */
    public function setContentType($contentType)
    {
        $this->content_type = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Set needsBlockRefresh
     *
     * @param boolean $needsBlockRefresh
     *
     * @return EmailingCampaign
     */
    public function setNeedsBlockRefresh($needsBlockRefresh)
    {
        $this->needs_block_refresh = $needsBlockRefresh;

        return $this;
    }

    /**
     * Get needsBlockRefresh
     *
     * @return boolean
     */
    public function getNeedsBlockRefresh()
    {
        return $this->needs_block_refresh;
    }

    /**
     * Set recipients
     *
     * @param array $recipients
     *
     * @return EmailingCampaign
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Get recipients
     *
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Set settings
     *
     * @param array $settings
     *
     * @return EmailingCampaign
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set variateSettings
     *
     * @param array $variateSettings
     *
     * @return EmailingCampaign
     */
    public function setVariateSettings($variateSettings)
    {
        $this->variate_settings = $variateSettings;

        return $this;
    }

    /**
     * Get variateSettings
     *
     * @return array
     */
    public function getVariateSettings()
    {
        return $this->variate_settings;
    }

    /**
     * Set tracking
     *
     * @param array $tracking
     *
     * @return EmailingCampaign
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;

        return $this;
    }

    /**
     * Get tracking
     *
     * @return array
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * Set rssOpts
     *
     * @param array $rssOpts
     *
     * @return EmailingCampaign
     */
    public function setRssOpts($rssOpts)
    {
        $this->rss_opts = $rssOpts;

        return $this;
    }

    /**
     * Get rssOpts
     *
     * @return array
     */
    public function getRssOpts()
    {
        return $this->rss_opts;
    }

    /**
     * Set abSplitOpts
     *
     * @param array $abSplitOpts
     *
     * @return EmailingCampaign
     */
    public function setAbSplitOpts($abSplitOpts)
    {
        $this->ab_split_opts = $abSplitOpts;

        return $this;
    }

    /**
     * Get abSplitOpts
     *
     * @return array
     */
    public function getAbSplitOpts()
    {
        return $this->ab_split_opts;
    }

    /**
     * Set socialCard
     *
     * @param array $socialCard
     *
     * @return EmailingCampaign
     */
    public function setSocialCard($socialCard)
    {
        $this->social_card = $socialCard;

        return $this;
    }

    /**
     * Get socialCard
     *
     * @return array
     */
    public function getSocialCard()
    {
        return $this->social_card;
    }

    /**
     * Set reportSummary
     *
     * @param array $reportSummary
     *
     * @return EmailingCampaign
     */
    public function setReportSummary($reportSummary)
    {
        $this->report_summary = $reportSummary;

        return $this;
    }

    /**
     * Get reportSummary
     *
     * @return array
     */
    public function getReportSummary()
    {
        return $this->report_summary;
    }
}
