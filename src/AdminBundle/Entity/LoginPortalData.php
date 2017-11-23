<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="login_portal_data")
 */
class LoginPortalData
{
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program", mappedBy="login_portal_data")
     */
    private $program;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\LoginPortalSlide", mappedBy="login_portal_data")
     */
    private $login_portal_slides;

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
     * Set title
     *
     * @param string $title
     *
     * @return LoginPortalData
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->login_portal_slides = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return LoginPortalData
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return LoginPortalData
     */
    public function setProgram(\AdminBundle\Entity\Program $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return \AdminBundle\Entity\Program
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Add loginPortalSlide
     *
     * @param \AdminBundle\Entity\LoginPortalSlide $loginPortalSlide
     *
     * @return LoginPortalData
     */
    public function addLoginPortalSlide(\AdminBundle\Entity\LoginPortalSlide $loginPortalSlide)
    {
        $this->login_portal_slides[] = $loginPortalSlide;

        return $this;
    }

    /**
     * Remove loginPortalSlide
     *
     * @param \AdminBundle\Entity\LoginPortalSlide $loginPortalSlide
     */
    public function removeLoginPortalSlide(\AdminBundle\Entity\LoginPortalSlide $loginPortalSlide)
    {
        $this->login_portal_slides->removeElement($loginPortalSlide);
    }

    /**
     * Get loginPortalSlides
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLoginPortalSlides()
    {
        return $this->login_portal_slides;
    }
}
