<?php
namespace AdminBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A DTO class for holding data when manipulating campaign draft (e.g. : create campaign draft)
 */
class CampaignDraftData
{
    private $id;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     */
    private $name;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     */
    private $subject;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     * @Assert\NotNull(groups={"normal_creation_mode"})
     */
    private $listId;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     * @Assert\NotNull(groups={"normal_creation_mode"})
     */
    private $templateId;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     */
    private $programmedState;

    private $programmedLaunchDate;

    /**
     * Set id
     *
     * @param int $id
     *
     * @return CampaignDraftData
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return null|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param $name
     *
     * @return CampaignDraftData
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set subject
     *
     * @param $subject
     *
     * @return CampaignDraftData
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
     * Set listId
     *
     * @param int $listId
     *
     * @return CampaignDraftData
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Get listId
     *
     * @return int
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * Set templateId
     *
     * @param $templateId
     *
     * @return CampaignDraftData
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;

        return $this;
    }

    /**
     * Get templateId
     *
     * @return int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set programmedState
     *
     * @param $programmedState
     *
     * @return CampaignDraftData
     */
    public function setProgrammedState($programmedState)
    {
        $this->programmedState = $programmedState;

        return $this;
    }

    /**
     * Get programmedState
     *
     * @return bool
     */
    public function getProgrammedState()
    {
        return $this->programmedState;
    }

    /**
     * Set programmedLaunchDate
     *
     * @param \DateTime $date
     *
     * @return CampaignDraftData
     */
    public function setProgrammedLaunchDate(\DateTime $date)
    {
        $this->programmedLaunchDate = $date;

        return $this;
    }

    /**
     * Get programmedLaunchDate
     *
     * @return null|DateTime
     */
    public function getProgrammedLaunchDate()
    {
        return $this->programmedLaunchDate;
    }
}
