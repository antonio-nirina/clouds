<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_post")
 */
class NewsPost
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $action_button_state;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $action_button_target_page;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\HomePagePost", mappedBy="news_post")
     */
    private $home_page_post;

    /**
     * @var string $viewer_authorization_type       available value in AdminBundle\Component\Post\NewsPostAuthorizationType
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=20)
     * @Assert\NotBlank()
     */
    private $viewer_authorization_type;

    /**
     * @var string $authorized_viewer_role          AdminBundle\Entity\ProgramUser role value
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $authorized_viewer_role;

    /**
     * @var string $custom_authorized_viewer_list   list of authorized AdminBundle\Entity\ProgramUser id
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $custom_authorized_viewer_list;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $programmed_publication_state;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $programmed_publication_datetime;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $archived_state;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $published_state;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $view_number;

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
     * Set actionButtonState
     *
     * @param boolean $actionButtonState
     *
     * @return NewsPost
     */
    public function setActionButtonState($actionButtonState)
    {
        $this->action_button_state = $actionButtonState;

        return $this;
    }

    /**
     * Get actionButtonState
     *
     * @return boolean
     */
    public function getActionButtonState()
    {
        return $this->action_button_state;
    }

    /**
     * Set actionButtonTextColor
     *
     * @param string $actionButtonTextColor
     *
     * @return NewsPost
     */
    public function setActionButtonTextColor($actionButtonTextColor)
    {
        $this->action_button_text_color = $actionButtonTextColor;

        return $this;
    }

    /**
     * Get actionButtonTextColor
     *
     * @return string
     */
    public function getActionButtonTextColor()
    {
        return $this->action_button_text_color;
    }

    /**
     * Set actionButtonBackgroundColor
     *
     * @param string $actionButtonBackgroundColor
     *
     * @return NewsPost
     */
    public function setActionButtonBackgroundColor($actionButtonBackgroundColor)
    {
        $this->action_button_background_color = $actionButtonBackgroundColor;

        return $this;
    }

    /**
     * Get actionButtonBackgroundColor
     *
     * @return string
     */
    public function getActionButtonBackgroundColor()
    {
        return $this->action_button_background_color;
    }

    /**
     * Set homePagePost
     *
     * @param \AdminBundle\Entity\HomePagePost $homePagePost
     *
     * @return NewsPost
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

    /**
     * Set actionButtonText
     *
     * @param string $actionButtonText
     *
     * @return NewsPost
     */
    public function setActionButtonText($actionButtonText)
    {
        $this->action_button_text = $actionButtonText;

        return $this;
    }

    /**
     * Get actionButtonText
     *
     * @return string
     */
    public function getActionButtonText()
    {
        return $this->action_button_text;
    }

    /**
     * Set viewerAuthorizationType
     *
     * @param string $viewerAuthorizationType
     *
     * @return NewsPost
     */
    public function setViewerAuthorizationType($viewerAuthorizationType)
    {
        $this->viewer_authorization_type = $viewerAuthorizationType;

        return $this;
    }

    /**
     * Get viewerAuthorizationType
     *
     * @return string
     */
    public function getViewerAuthorizationType()
    {
        return $this->viewer_authorization_type;
    }

    /**
     * Set authorizedViewerRole
     *
     * @param string $authorizedViewerRole
     *
     * @return NewsPost
     */
    public function setAuthorizedViewerRole($authorizedViewerRole)
    {
        $this->authorized_viewer_role = $authorizedViewerRole;

        return $this;
    }

    /**
     * Get authorizedViewerRole
     *
     * @return string
     */
    public function getAuthorizedViewerRole()
    {
        return $this->authorized_viewer_role;
    }

    /**
     * Set customAuthorizedViewerList
     *
     * @param array $customAuthorizedViewerList
     *
     * @return NewsPost
     */
    public function setCustomAuthorizedViewerList($customAuthorizedViewerList)
    {
        $this->custom_authorized_viewer_list = $customAuthorizedViewerList;

        return $this;
    }

    /**
     * Get customAuthorizedViewerList
     *
     * @return array
     */
    public function getCustomAuthorizedViewerList()
    {
        return $this->custom_authorized_viewer_list;
    }

    /**
     * Set programmedPublicationState
     *
     * @param boolean $programmedPublicationState
     *
     * @return NewsPost
     */
    public function setProgrammedPublicationState($programmedPublicationState)
    {
        $this->programmed_publication_state = $programmedPublicationState;

        return $this;
    }

    /**
     * Get programmedPublicationState
     *
     * @return boolean
     */
    public function getProgrammedPublicationState()
    {
        return $this->programmed_publication_state;
    }

    /**
     * Set programmedPublicationDatetime
     *
     * @param \DateTime $programmedPublicationDatetime
     *
     * @return NewsPost
     */
    public function setProgrammedPublicationDatetime($programmedPublicationDatetime)
    {
        $this->programmed_publication_datetime = $programmedPublicationDatetime;

        return $this;
    }

    /**
     * Get programmedPublicationDatetime
     *
     * @return \DateTime
     */
    public function getProgrammedPublicationDatetime()
    {
        return $this->programmed_publication_datetime;
    }

    /**
     * Set archivedState
     *
     * @param boolean $archivedState
     *
     * @return NewsPost
     */
    public function setArchivedState($archivedState)
    {
        $this->archived_state = $archivedState;

        return $this;
    }

    /**
     * Get archivedState
     *
     * @return boolean
     */
    public function getArchivedState()
    {
        return $this->archived_state;
    }

    /**
     * Set publishedState
     *
     * @param boolean $publishedState
     *
     * @return NewsPost
     */
    public function setPublishedState($publishedState)
    {
        $this->published_state = $publishedState;

        return $this;
    }

    /**
     * Get publishedState
     *
     * @return boolean
     */
    public function getPublishedState()
    {
        return $this->published_state;
    }

    /**
     * Set viewNumber
     *
     * @param integer $viewNumber
     *
     * @return NewsPost
     */
    public function setViewNumber($viewNumber)
    {
        $this->view_number = $viewNumber;

        return $this;
    }

    /**
     * Get viewNumber
     *
     * @return integer
     */
    public function getViewNumber()
    {
        return $this->view_number;
    }

    /**
     * Set actionButtonTargetUrl
     *
     * @param string $actionButtonTargetUrl
     *
     * @return NewsPost
     */
    public function setActionButtonTargetUrl($actionButtonTargetUrl)
    {
        $this->action_button_target_url = $actionButtonTargetUrl;

        return $this;
    }

    /**
     * Get actionButtonTargetUrl
     *
     * @return string
     */
    public function getActionButtonTargetUrl()
    {
        return $this->action_button_target_url;
    }

    /**
     * Set actionButtonTargetPage
     *
     * @param string $actionButtonTargetPage
     *
     * @return NewsPost
     */
    public function setActionButtonTargetPage($actionButtonTargetPage)
    {
        $this->action_button_target_page = $actionButtonTargetPage;

        return $this;
    }

    /**
     * Get actionButtonTargetPage
     *
     * @return string
     */
    public function getActionButtonTargetPage()
    {
        return $this->action_button_target_page;
    }
}
