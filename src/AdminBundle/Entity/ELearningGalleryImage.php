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
     */
    private $image_file;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ELearningMediaContent", inversedBy="images")
     */
    private $e_learning_media_content;
}
