<?php
namespace AdminBundle\DTO;

/**
 * A DTO class for holding data when manipulating campaign draft (e.g. : create campaign draft)
 */
class CampaignDraftData
{
    private $name;
    private $object;
    private $list_id;
    private $template_id;
    private $programmed_state;

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
     * Set object
     *
     * @param $object
     *
     * @return CampaignDraftData
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
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
}