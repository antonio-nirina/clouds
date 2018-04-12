<?php
namespace AdminBundle\Traits\EntityTraits;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Grouping action button datas
 */
trait ActionButtonTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $action_button_text;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $action_button_text_color;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $action_button_background_color;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $action_button_target_url;

    /**
     * Target page. Relative url to traget page
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $action_button_target_page;
}
