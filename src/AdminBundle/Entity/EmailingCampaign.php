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
     * @ORM\Column(name="AXFractionName", type="string")
     */
    private $AX_fraction_name;

    /**
     * @ORM\Column(name="contact_list_id", type="integer")
     */
    private $contacts_list_id;

    /**
     * @ORM\Column(name="created_at", type="string")
     */
    private $created_at;

    /**
     * @ORM\Column(name="current", type="integer")
     */
    private $current;

    /**
     * @ORM\Column(name="delivered_at", type="string")
     */
    private $delivered_at;

    /**
     * @ORM\Column(name="edit_mode", type="string")
     */
    private $edit_mode;

    /**
     * @ORM\Column(name="ID", type="integer")
     */
    private $ID;

    /**
     * @ORM\Column(name="is_starred", type="boolean")
     */
    private $is_starred;

    /**
     * @ORM\Column(name="is_text_part_included", type="boolean")
     */
    private $is_text_part_included;

    /**
     * @ORM\Column(name="locale", type="string")
     */
    private $locale;

    /**
     * @ORM\Column(name="modified_at", type="string")
     */
    private $modified_at;

    /**
     * @ORM\Column(name="preset", type="object")
     */
    private $preset;

    /**
     * @ORM\Column(name="sender", type="string")
     */
    private $sender;

    /**
     * @ORM\Column(name="sender_email", type="string")
     */
    private $sender_email;

    /**
     * @ORM\Column(name="sender_name", type="string")
     */
    private $sender_name;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @ORM\Column(name="subject", type="text")
     */
    private $subject;

    /**
     * @ORM\Column(name="template_id", type="integer")
     */
    private $template_id;

    /**
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @ORM\Column(name="used", type="boolean")
     */
    private $used;

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
     * Set aXFractionName
     *
     * @param string $aXFractionName
     *
     * @return EmailingCampaign
     */
    public function setAXFractionName($aXFractionName)
    {
        $this->AX_fraction_name = $aXFractionName;

        return $this;
    }

    /**
     * Get aXFractionName
     *
     * @return string
     */
    public function getAXFractionName()
    {
        return $this->AX_fraction_name;
    }

    /**
     * Set contactsListId
     *
     * @param integer $contactsListId
     *
     * @return EmailingCampaign
     */
    public function setContactsListId($contactsListId)
    {
        $this->contacts_list_id = $contactsListId;

        return $this;
    }

    /**
     * Get contactsListId
     *
     * @return integer
     */
    public function getContactsListId()
    {
        return $this->contacts_list_id;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return EmailingCampaign
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set current
     *
     * @param integer $current
     *
     * @return EmailingCampaign
     */
    public function setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get current
     *
     * @return integer
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set deliveredAt
     *
     * @param string $deliveredAt
     *
     * @return EmailingCampaign
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->delivered_at = $deliveredAt;

        return $this;
    }

    /**
     * Get deliveredAt
     *
     * @return string
     */
    public function getDeliveredAt()
    {
        return $this->delivered_at;
    }

    /**
     * Set editMode
     *
     * @param string $editMode
     *
     * @return EmailingCampaign
     */
    public function setEditMode($editMode)
    {
        $this->edit_mode = $editMode;

        return $this;
    }

    /**
     * Get editMode
     *
     * @return string
     */
    public function getEditMode()
    {
        return $this->edit_mode;
    }

    /**
     * Set isStarred
     *
     * @param boolean $isStarred
     *
     * @return EmailingCampaign
     */
    public function setIsStarred($isStarred)
    {
        $this->is_starred = $isStarred;

        return $this;
    }

    /**
     * Get isStarred
     *
     * @return boolean
     */
    public function getIsStarred()
    {
        return $this->is_starred;
    }

    /**
     * Set isTextPartIncluded
     *
     * @param boolean $isTextPartIncluded
     *
     * @return EmailingCampaign
     */
    public function setIsTextPartIncluded($isTextPartIncluded)
    {
        $this->is_text_part_included = $isTextPartIncluded;

        return $this;
    }

    /**
     * Get isTextPartIncluded
     *
     * @return boolean
     */
    public function getIsTextPartIncluded()
    {
        return $this->is_text_part_included;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return EmailingCampaign
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set modifiedAt
     *
     * @param string $modifiedAt
     *
     * @return EmailingCampaign
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modified_at = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return string
     */
    public function getModifiedAt()
    {
        return $this->modified_at;
    }

    /**
     * Set preset
     *
     * @param \stdClass $preset
     *
     * @return EmailingCampaign
     */
    public function setPreset($preset)
    {
        $this->preset = $preset;

        return $this;
    }

    /**
     * Get preset
     *
     * @return \stdClass
     */
    public function getPreset()
    {
        return $this->preset;
    }

    /**
     * Set sender
     *
     * @param string $sender
     *
     * @return EmailingCampaign
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set senderEmail
     *
     * @param string $senderEmail
     *
     * @return EmailingCampaign
     */
    public function setSenderEmail($senderEmail)
    {
        $this->sender_email = $senderEmail;

        return $this;
    }

    /**
     * Get senderEmail
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->sender_email;
    }

    /**
     * Set senderName
     *
     * @param string $senderName
     *
     * @return EmailingCampaign
     */
    public function setSenderName($senderName)
    {
        $this->sender_name = $senderName;

        return $this;
    }

    /**
     * Get senderName
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->sender_name;
    }

    /**
     * Set status
     *
     * @param integer $status
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
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EmailingCampaign
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set templateId
     *
     * @param integer $templateId
     *
     * @return EmailingCampaign
     */
    public function setTemplateId($templateId)
    {
        $this->template_id = $templateId;

        return $this;
    }

    /**
     * Get templateId
     *
     * @return integer
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return EmailingCampaign
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return EmailingCampaign
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set used
     *
     * @param boolean $used
     *
     * @return EmailingCampaign
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     *
     * @return boolean
     */
    public function getUsed()
    {
        return $this->used;
    }
}
