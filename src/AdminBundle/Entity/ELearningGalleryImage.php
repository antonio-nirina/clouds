<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="e_learning_gallery_image")
 */
class ELearningGalleryImage
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
    private $image_order;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Image(maxSize="20M", mimeTypes={"image/jpeg", "image/gif"})
     */
    private $image_file;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ELearningMediaContent", inversedBy="images")
     */
    private $e_learning_media_content;

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
     * @return ELearningGalleryImage
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
     * Set imageOrder
     *
     * @param integer $imageOrder
     *
     * @return ELearningGalleryImage
     */
    public function setImageOrder($imageOrder)
    {
        $this->image_order = $imageOrder;

        return $this;
    }

    /**
     * Get imageOrder
     *
     * @return integer
     */
    public function getImageOrder()
    {
        return $this->image_order;
    }

    /**
     * Set imageFile
     *
     * @param string $imageFile
     *
     * @return ELearningGalleryImage
     */
    public function setImageFile($imageFile)
    {
        $this->image_file = $imageFile;

        return $this;
    }

    /**
     * Get imageFile
     *
     * @return string
     */
    public function getImageFile()
    {
        return $this->image_file;
    }

    /**
     * Set eLearningMediaContent
     *
     * @param \AdminBundle\Entity\ELearningMediaContent $eLearningMediaContent
     *
     * @return ELearningGalleryImage
     */
    public function setELearningMediaContent(\AdminBundle\Entity\ELearningMediaContent $eLearningMediaContent = null)
    {
        $this->e_learning_media_content = $eLearningMediaContent;

        return $this;
    }

    /**
     * Get eLearningMediaContent
     *
     * @return \AdminBundle\Entity\ELearningMediaContent
     */
    public function getELearningMediaContent()
    {
        return $this->e_learning_media_content;
    }
}
