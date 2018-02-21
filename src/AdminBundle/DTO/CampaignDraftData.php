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
    private $list_id;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     * @Assert\NotNull(groups={"normal_creation_mode"})
     */
    private $template_id;

    /**
     * @Assert\NotBlank(groups={"normal_creation_mode"})
     */
    private $programmed_state;

    private $programmed_launch_date;

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
     * Set list_id
     *
     * @param int $list_id
     *
     * @return CampaignDraftData
     */
    public function setListId($list_id)
    {
        $this->list_id = $list_id;

        return $this;
    }

    /**
     * Get list_id
     *
     * @return int
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * Set template_id
     *
     * @param $template_id
     *
     * @return CampaignDraftData
     */
    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;

        return $this;
    }

    /**
     * Get template_id
     *
     * @return int
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * Set programmed_state
     *
     * @param $programmed_state
     *
     * @return CampaignDraftData
     */
    public function setProgrammedState($programmed_state)
    {
        $this->programmed_state = $programmed_state;

        return $this;
    }

    /**
     * Get programmed_state
     *
     * @return bool
     */
    public function getProgrammedState()
    {
        return $this->programmed_state;
    }

    /**
     * Set programmed_launch_date
     *
     * @param \DateTime $date
     *
     * @return CampaignDraftData
     */
    public function setProgrammedLaunchDate(\DateTime $date)
    {
        $this->programmed_launch_date = $date;

        return $this;
    }

    /**
     * Get programmed_launch_date
     *
     * @return null|DateTime
     */
    public function getProgrammedLaunchDate()
    {
        return $this->programmed_launch_date;
    }
}