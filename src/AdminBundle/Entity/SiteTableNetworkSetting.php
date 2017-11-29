<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="site_table_network_setting")
 */
class SiteTableNetworkSetting
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="has_classment", type="boolean")
     */
    private $has_classment;

    /**
     * @ORM\Column(name="has_like", type="boolean")
     */
    private $has_like;

    /**
     * @ORM\Column(name="has_facebook", type="boolean")
     */
    private $has_facebook;

    /**
     * @ORM\Column(name="facebook_link", type="text", nullable=true)
     */
    private $facebook_link;

    /**
     * @ORM\Column(name="has_linkedin", type="boolean")
     */
    private $has_linkedin;

    /**
     * @ORM\Column(name="linkedin_link", type="text", nullable=true)
     */
    private $linkedin_link;

    /**
     * @ORM\Column(name="has_twitter", type="boolean")
     */
    private $has_twitter;

    /**
     * @ORM\Column(name="twitter_link", type="text", nullable=true)
     */
    private $twitter_link;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="site_table_network_setting")
     */
    private $program;

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
     * Set hasClassment
     *
     * @param boolean $hasClassment
     *
     * @return SiteTableNetworkSetting
     */
    public function setHasClassment($hasClassment)
    {
        $this->has_classment = $hasClassment;

        return $this;
    }

    /**
     * Get hasClassment
     *
     * @return boolean
     */
    public function getHasClassment()
    {
        return $this->has_classment;
    }

    /**
     * Set hasLike
     *
     * @param boolean $hasLike
     *
     * @return SiteTableNetworkSetting
     */
    public function setHasLike($hasLike)
    {
        $this->has_like = $hasLike;

        return $this;
    }

    /**
     * Get hasLike
     *
     * @return boolean
     */
    public function getHasLike()
    {
        return $this->has_like;
    }

    /**
     * Set hasFacebook
     *
     * @param boolean $hasFacebook
     *
     * @return SiteTableNetworkSetting
     */
    public function setHasFacebook($hasFacebook)
    {
        $this->has_facebook = $hasFacebook;

        return $this;
    }

    /**
     * Get hasFacebook
     *
     * @return boolean
     */
    public function getHasFacebook()
    {
        return $this->has_facebook;
    }

    /**
     * Set facebookLink
     *
     * @param string $facebookLink
     *
     * @return SiteTableNetworkSetting
     */
    public function setFacebookLink($facebookLink)
    {
        $this->facebook_link = $facebookLink;

        return $this;
    }

    /**
     * Get facebookLink
     *
     * @return string
     */
    public function getFacebookLink()
    {
        return $this->facebook_link;
    }

    /**
     * Set hasLinkedin
     *
     * @param boolean $hasLinkedin
     *
     * @return SiteTableNetworkSetting
     */
    public function setHasLinkedin($hasLinkedin)
    {
        $this->has_linkedin = $hasLinkedin;

        return $this;
    }

    /**
     * Get hasLinkedin
     *
     * @return boolean
     */
    public function getHasLinkedin()
    {
        return $this->has_linkedin;
    }

    /**
     * Set linkedinLink
     *
     * @param string $linkedinLink
     *
     * @return SiteTableNetworkSetting
     */
    public function setLinkedinLink($linkedinLink)
    {
        $this->linkedin_link = $linkedinLink;

        return $this;
    }

    /**
     * Get linkedinLink
     *
     * @return string
     */
    public function getLinkedinLink()
    {
        return $this->linkedin_link;
    }

    /**
     * Set hasTwitter
     *
     * @param boolean $hasTwitter
     *
     * @return SiteTableNetworkSetting
     */
    public function setHasTwitter($hasTwitter)
    {
        $this->has_twitter = $hasTwitter;

        return $this;
    }

    /**
     * Get hasTwitter
     *
     * @return boolean
     */
    public function getHasTwitter()
    {
        return $this->has_twitter;
    }

    /**
     * Set twitterLink
     *
     * @param string $twitterLink
     *
     * @return SiteTableNetworkSetting
     */
    public function setTwitterLink($twitterLink)
    {
        $this->twitter_link = $twitterLink;

        return $this;
    }

    /**
     * Get twitterLink
     *
     * @return string
     */
    public function getTwitterLink()
    {
        return $this->twitter_link;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return SiteTableNetworkSetting
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
}
