<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\HomePageDataRepository")
 * @ORM\Table(name="home_page_data")
 */
class HomePageData
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $editorial;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program", mappedBy="home_page_data")
     */
    private $program;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\HomePageSlide", mappedBy="home_page_data")
     * @Assert\Valid()
     */
    private $home_page_slides;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->home_page_slides = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set editorial
     *
     * @param string $editorial
     *
     * @return HomePageData
     */
    public function setEditorial($editorial)
    {
        $this->editorial = $editorial;

        return $this;
    }

    /**
     * Get editorial
     *
     * @return string
     */
    public function getEditorial()
    {
        return $this->editorial;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return HomePageData
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
     * Add homePageSlide
     *
     * @param \AdminBundle\Entity\HomePageSlide $homePageSlide
     *
     * @return HomePageData
     */
    public function addHomePageSlide(\AdminBundle\Entity\HomePageSlide $homePageSlide)
    {
        $this->home_page_slides[] = $homePageSlide;

        return $this;
    }

    /**
     * Remove homePageSlide
     *
     * @param \AdminBundle\Entity\HomePageSlide $homePageSlide
     */
    public function removeHomePageSlide(\AdminBundle\Entity\HomePageSlide $homePageSlide)
    {
        $this->home_page_slides->removeElement($homePageSlide);
    }

    /**
     * Get homePageSlides
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHomePageSlides()
    {
        return $this->home_page_slides;
    }
}
