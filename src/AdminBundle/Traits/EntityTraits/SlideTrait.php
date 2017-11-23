<?php
namespace AdminBundle\Traits\EntityTraits;

trait SlideTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $slide_order;
}

