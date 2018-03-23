<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Traits\EntityTraits\ELearningContentTrait;
use AdminBundle\Traits\EntityTraits\ActionButtonTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="e_learning_button_content")
 */
class ELearningButtonContent
{
    use ELearningContentTrait;
    use ActionButtonTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ELearning", inversedBy="button_contents")
     */
    private $e_learning;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ELearningButtonContent
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
     * Set contentOrder
     *
     * @param integer $contentOrder
     *
     * @return ELearningButtonContent
     */
    public function setContentOrder($contentOrder)
    {
        $this->content_order = $contentOrder;

        return $this;
    }

    /**
     * Get contentOrder
     *
     * @return integer
     */
    public function getContentOrder()
    {
        return $this->content_order;
    }

    /**
     * Set actionButtonText
     *
     * @param string $actionButtonText
     *
     * @return ELearningButtonContent
     */
    public function setActionButtonText($actionButtonText)
    {
        $this->action_button_text = $actionButtonText;

        return $this;
    }

    /**
     * Get actionButtonText
     *
     * @return string
     */
    public function getActionButtonText()
    {
        return $this->action_button_text;
    }

    /**
     * Set actionButtonTextColor
     *
     * @param string $actionButtonTextColor
     *
     * @return ELearningButtonContent
     */
    public function setActionButtonTextColor($actionButtonTextColor)
    {
        $this->action_button_text_color = $actionButtonTextColor;

        return $this;
    }

    /**
     * Get actionButtonTextColor
     *
     * @return string
     */
    public function getActionButtonTextColor()
    {
        return $this->action_button_text_color;
    }

    /**
     * Set actionButtonBackgroundColor
     *
     * @param string $actionButtonBackgroundColor
     *
     * @return ELearningButtonContent
     */
    public function setActionButtonBackgroundColor($actionButtonBackgroundColor)
    {
        $this->action_button_background_color = $actionButtonBackgroundColor;

        return $this;
    }

    /**
     * Get actionButtonBackgroundColor
     *
     * @return string
     */
    public function getActionButtonBackgroundColor()
    {
        return $this->action_button_background_color;
    }

    /**
     * Set actionButtonTargetUrl
     *
     * @param string $actionButtonTargetUrl
     *
     * @return ELearningButtonContent
     */
    public function setActionButtonTargetUrl($actionButtonTargetUrl)
    {
        $this->action_button_target_url = $actionButtonTargetUrl;

        return $this;
    }

    /**
     * Get actionButtonTargetUrl
     *
     * @return string
     */
    public function getActionButtonTargetUrl()
    {
        return $this->action_button_target_url;
    }

    /**
     * Set actionButtonTargetPage
     *
     * @param string $actionButtonTargetPage
     *
     * @return ELearningButtonContent
     */
    public function setActionButtonTargetPage($actionButtonTargetPage)
    {
        $this->action_button_target_page = $actionButtonTargetPage;

        return $this;
    }

    /**
     * Get actionButtonTargetPage
     *
     * @return string
     */
    public function getActionButtonTargetPage()
    {
        return $this->action_button_target_page;
    }

    /**
     * Set eLearning
     *
     * @param \AdminBundle\Entity\ELearning $eLearning
     *
     * @return ELearningButtonContent
     */
    public function setELearning(\AdminBundle\Entity\ELearning $eLearning = null)
    {
        $this->e_learning = $eLearning;

        return $this;
    }

    /**
     * Get eLearning
     *
     * @return \AdminBundle\Entity\ELearning
     */
    public function getELearning()
    {
        return $this->e_learning;
    }
}
