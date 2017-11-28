<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="editorial")
 */
class Editorial
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
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_edit;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\HomePageData", inversedBy="editorial")
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
     * Set content
     *
     * @param string $content
     *
     * @return Editorial
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
     * Set lastEdit
     *
     * @param \DateTime $lastEdit
     *
     * @return Editorial
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
     * Set homePageData
     *
     * @param \AdminBundle\Entity\HomePageData $homePageData
     *
     * @return Editorial
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
