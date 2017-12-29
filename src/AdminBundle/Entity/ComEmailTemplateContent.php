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
     * @ORM\Column(type="string", nullable=true)
     */
    private $text_content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Image(
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
}
