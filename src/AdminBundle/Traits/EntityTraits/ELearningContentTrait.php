<?php
namespace AdminBundle\Traits\EntityTraits;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

trait ELearningContentTrait
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
     * @var $content_order      can be null for action button content
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $content_order;
}
