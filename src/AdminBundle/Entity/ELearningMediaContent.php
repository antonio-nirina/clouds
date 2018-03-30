<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AdminBundle\Traits\EntityTraits\ELearningContentTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AdminBundle\Component\FileUpload\DocumentFileBlackList;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="e_learning_media_content")
 */
class ELearningMediaContent
{
    use ELearningContentTrait;

    /**
     * @var $content_type    taking value in AdminBundle\Component\Elearning\ELearningContentType constant
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $content_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\File(maxSize="10M")
     */
    private $associated_file;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $video_url;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearningGalleryImage", mappedBy="e_learning_media_content")
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Elearning", inversedBy="media_contents")
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
     * Set contentType
     *
     * @param string $contentType
     *
     * @return ELearningMediaContent
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
     * @return ELearningMediaContent
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
     * @return ELearningMediaContent
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
     * @return ELearningMediaContent
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
     * @return ELearningMediaContent
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
     * Add image
     *
     * @param \AdminBundle\Entity\ELearningGalleryImage $image
     *
     * @return ELearningMediaContent
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
     * @return ELearningMediaContent
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


    /**
     * Validate associated file on upoad
     * Used with document type media
     *
     * @param ExecutionContextInterface $context
     * @param $payload
     *
     * @Assert\Callback()
     */
    public function validateAssociatedFile(ExecutionContextInterface $context, $payload)
    {
        $associate_file = $this->getAssociatedFile();
        if (!is_null($associate_file) && $associate_file instanceof UploadedFile) {
            $file_extension = $associate_file->getClientOriginalExtension();
            if (in_array($file_extension, DocumentFileBlackList::BLACK_LIST)) {
                $context->buildViolation('Type de fichier non autorisé.')
                    ->atPath('associated_file')
                    ->addViolation();
            }
        }
    }
}
