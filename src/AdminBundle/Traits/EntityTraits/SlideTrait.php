<?php
namespace AdminBundle\Traits\EntityTraits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SlideTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image(
     *     maxSize = "8M",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"}
     * )
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $slide_order;
}
