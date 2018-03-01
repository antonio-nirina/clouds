<?php
namespace AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\HomePagePostRepository")
 * @ORM\Table(name="home_page_post")
 */
class HomePagePost
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $last_edit;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="home_page_post")
     */
    private $program;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\HomePagePostSlide", mappedBy="home_page_post")
     * @Assert\Valid()
     */
    private $home_page_post_slides;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $post_type;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\NewsPost", inversedBy="home_page_post")
     */
    private $news_post;
    
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
     * Set title
     *
     * @param string $title
     *
     * @return HomePagePost
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
     * Set content
     *
     * @param string $content
     *
     * @return HomePagePost
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return HomePagePost
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set lastEdit
     *
     * @param \DateTime $lastEdit
     *
     * @return HomePagePost
     */
    public function setLastEdit($lastEdit)
    {
        $this->last_edit = $lastEdit;

        return $this;
    }

    /**
     * Get lastEdit
     *
     * @return \DateTime
     */
    public function getLastEdit()
    {
        return $this->last_edit;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return HomePagePost
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
     * Add homePagePostSlide
     *
     * @param \AdminBundle\Entity\HomePagePostSlide $homePagePostSlide
     *
     * @return HomePagePost
     */
    public function addHomePagePostSlide(\AdminBundle\Entity\HomePagePostSlide $homePagePostSlide)
    {
        $this->home_page_post_slides[] = $homePagePostSlide;

        return $this;
    }

    /**
     * Remove homePagePostSlide
     *
     * @param \AdminBundle\Entity\HomePagePostSlide $homePagePostSlide
     */
    public function removeHomePagePostSlide(\AdminBundle\Entity\HomePagePostSlide $homePagePostSlide)
    {
        $this->home_page_post_slides->removeElement($homePagePostSlide);
    }

    /**
     * Get homePagePostSlides
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHomePagePostSlides()
    {
        return $this->home_page_post_slides;
    }

    /**
     * Set postType
     *
     * @param string $postType
     *
     * @return HomePagePost
     */
    public function setPostType($postType)
    {
        $this->post_type = $postType;

        return $this;
    }

    /**
     * Get postType
     *
     * @return string
     */
    public function getPostType()
    {
        return $this->post_type;
    }

    /**
     * Set newsPost
     *
     * @param \AdminBundle\Entity\NewsPost $newsPost
     *
     * @return HomePagePost
     */
    public function setNewsPost(\AdminBundle\Entity\NewsPost $newsPost = null)
    {
        $this->news_post = $newsPost;

        return $this;
    }

    /**
     * Get newsPost
     *
     * @return \AdminBundle\Entity\NewsPost
     */
    public function getNewsPost()
    {
        return $this->news_post;
    }
}
