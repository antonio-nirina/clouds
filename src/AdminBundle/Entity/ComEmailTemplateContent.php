<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="com_email_template_content")
 */
class ComEmailTemplateContent
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text_content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Image(
     *     maxSize = "8M",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"}
     * )
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $content_type;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ComEmailTemplate", inversedBy="contents")
     */
    private $template;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $content_order;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $action_button_text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $action_button_url;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $action_button_background_color;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $action_button_text_color;

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
     * Set textContent
     *
     * @param string $textContent
     *
     * @return ComEmailTemplateContent
     */
    public function setTextContent($textContent)
    {
        $this->text_content = $textContent;

        return $this;
    }

    /**
     * Get textContent
     *
     * @return string
     */
    public function getTextContent()
    {
        return $this->text_content;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return ComEmailTemplateContent
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     *
     * @return ComEmailTemplateContent
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
     * Set template
     *
     * @param \AdminBundle\Entity\ComEmailTemplate $template
     *
     * @return ComEmailTemplateContent
     */
    public function setTemplate(\AdminBundle\Entity\ComEmailTemplate $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \AdminBundle\Entity\ComEmailTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set contentOrder
     *
     * @param integer $contentOrder
     *
     * @return ComEmailTemplateContent
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
     * @return ComEmailTemplateContent
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
     * Set actionButtonUrl
     *
     * @param string $actionButtonUrl
     *
     * @return ComEmailTemplateContent
     */
    public function setActionButtonUrl($actionButtonUrl)
    {
        $this->action_button_url = $actionButtonUrl;

        return $this;
    }

    /**
     * Get actionButtonUrl
     *
     * @return string
     */
    public function getActionButtonUrl()
    {
        return $this->action_button_url;
    }

    /**
     * Set actionButtonBackgroundColor
     *
     * @param string $actionButtonBackgroundColor
     *
     * @return ComEmailTemplateContent
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
     * Set actionButtonTextColor
     *
     * @param string $actionButtonTextColor
     *
     * @return ComEmailTemplateContent
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

    public function setIdToNull()
    {
        $this->id = null;

        return $this;
    }
}
