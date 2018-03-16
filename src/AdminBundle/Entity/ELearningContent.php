<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AdminBundle\Traits\EntityTraits\ActionButtonTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="e_learning_content")
 */
class ELearningContent
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $content_order;

    /**
     * @var $content_type    taking value in AdminBundle\Component\Elearning\ELearningContentType constant
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $content_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $associated_file;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $video_url;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearningGalleryImage", mappedBy="e_learning_content")
     */
    private $images;

    use ActionButtonTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ELearning", inversedBy="contents")
     */
    private $e_learning;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return ELearningContent
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
     * @return ELearningContent
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
     * Set contentType
     *
     * @param string $contentType
     *
     * @return ELearningContent
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
     * Set associatedFile
     *
     * @param string $associatedFile
     *
     * @return ELearningContent
     */
    public function setAssociatedFile($associatedFile)
    {
        $this->associated_file = $associatedFile;

        return $this;
    }

    /**
     * Get associatedFile
     *
     * @return string
     */
    public function getAssociatedFile()
    {
        return $this->associated_file;
    }

    /**
     * Set videoUrl
     *
     * @param string $videoUrl
     *
     * @return ELearningContent
     */
    public function setVideoUrl($videoUrl)
    {
        $this->video_url = $videoUrl;

        return $this;
    }

    /**
     * Get videoUrl
     *
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * Set actionButtonText
     *
     * @param string $actionButtonText
     *
     * @return ELearningContent
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
     * @return ELearningContent
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
     * @return ELearningContent
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
     * @return ELearningContent
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
     * @return ELearningContent
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
     * Add image
     *
     * @param \AdminBundle\Entity\ELearningGalleryImage $image
     *
     * @return ELearningContent
     */
    public function addImage(\AdminBundle\Entity\ELearningGalleryImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AdminBundle\Entity\ELearningGalleryImage $image
     */
    public function removeImage(\AdminBundle\Entity\ELearningGalleryImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set eLearning
     *
     * @param \AdminBundle\Entity\ELearning $eLearning
     *
     * @return ELearningContent
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
