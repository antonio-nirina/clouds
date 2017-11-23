<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Traits\EntityTraits\SlideTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="login_portal_slide")
 */
class LoginPortalSlide
{
    use SlideTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\LoginPortalData", inversedBy="login_portal_slides")
     */
    private $login_portal_data;

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
     * Set image
     *
     * @param string $image
     *
     * @return LoginPortalSlide
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
     * @return LoginPortalSlide
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
     * Set loginPortalData
     *
     * @param \AdminBundle\Entity\LoginPortalDat $loginPortalData
     *
     * @return LoginPortalSlide
     */
    public function setLoginPortalData(\AdminBundle\Entity\LoginPortalData $loginPortalData = null)
    {
        $this->login_portal_data = $loginPortalData;

        return $this;
    }

    /**
     * Get loginPortalData
     *
     * @return \AdminBundle\Entity\LoginPortalDat
     */
    public function getLoginPortalData()
    {
        return $this->login_portal_data;
    }
}
