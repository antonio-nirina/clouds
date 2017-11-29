<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Traits\EntityTraits\SlideTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\HomePageSlideRepository")
 * @ORM\Table(name="home_page_slide")
 */
class HomePageSlide
{
    use SlideTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\HomePageData", inversedBy="home_page_slides")
     */
    private $home_page_data;

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
     * Set message
     *
     * @param string $message
     *
     * @return HomePageSlide
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return HomePageSlide
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
     * Set slideOrder
     *
     * @param integer $slideOrder
     *
     * @return HomePageSlide
     */
    public function setSlideOrder($slideOrder)
    {
        $this->slide_order = $slideOrder;

        return $this;
    }

    /**
     * Get slideOrder
     *
     * @return integer
     */
    public function getSlideOrder()
    {
        return $this->slide_order;
    }

    /**
     * Set homePageData
     *
     * @param \AdminBundle\Entity\HomePageData $homePageData
     *
     * @return HomePageSlide
     */
    public function setHomePageData(\AdminBundle\Entity\HomePageData $homePageData = null)
    {
        $this->home_page_data = $homePageData;

        return $this;
    }

    /**
     * Get homePageData
     *
     * @return \AdminBundle\Entity\HomePageData
     */
    public function getHomePageData()
    {
        return $this->home_page_data;
    }
}
