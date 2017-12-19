<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Traits\EntityTraits\SlideTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\HomePagePostSlideRepository")
 * @ORM\Table(name="home_page_post_slide")
 */
class HomePagePostSlide
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $video_url;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(max=20)
     */
    private $slide_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $image_target_url;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\HomePagePost", inversedBy="home_page_post_slides")
     */
    private $home_page_post;

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
     * @return HomePagePostSlide
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
     * Set videoUrl
     *
     * @param string $videoUrl
     *
     * @return HomePagePostSlide
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
     * Set slideType
     *
     * @param string $slideType
     *
     * @return HomePagePostSlide
     */
    public function setSlideType($slideType)
    {
        $this->slide_type = $slideType;

        return $this;
    }

    /**
     * Get slideType
     *
     * @return string
     */
    public function getSlideType()
    {
        return $this->slide_type;
    }

    /**
     * Set imageTargetUrl
     *
     * @param string $imageTargetUrl
     *
     * @return HomePagePostSlide
     */
    public function setImageTargetUrl($imageTargetUrl)
    {
        $this->image_target_url = $imageTargetUrl;

        return $this;
    }

    /**
     * Get imageTargetUrl
     *
     * @return string
     */
    public function getImageTargetUrl()
    {
        return $this->image_target_url;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return HomePagePostSlide
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
     * @return HomePagePostSlide
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
     * Set homePagePost
     *
     * @param \AdminBundle\Entity\HomePagePost $homePagePost
     *
     * @return HomePagePostSlide
     */
    public function setHomePagePost(\AdminBundle\Entity\HomePagePost $homePagePost = null)
    {
        $this->home_page_post = $homePagePost;

        return $this;
    }

    /**
     * Get homePagePost
     *
     * @return \AdminBundle\Entity\HomePagePost
     */
    public function getHomePagePost()
    {
        return $this->home_page_post;
    }
}
