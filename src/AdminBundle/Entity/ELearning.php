<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="e_learning")
 */
class ELearning
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $main_text;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="e_learnings")
     */
    private $program;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published_state;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived_state;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $view_number;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publication_datetime;

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
     * @var string $viewer_authorization_type   available value in AdminBundle\Component\Authorization\AuthorizationType
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=100)
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
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearningMediaContent", mappedBy="e_learning")
     * @Assert\Valid()
     */
    private $media_contents;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearningQuizContent", mappedBy="e_learning")
     * @Assert\Valid()
     */
    private $quiz_contents;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearningButtonContent", mappedBy="e_learning")
     * @Assert\Valid()
     */
    private $button_contents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->media_contents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->quiz_contents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->button_contents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ELearning
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
     * Set mainText
     *
     * @param string $mainText
     *
     * @return ELearning
     */
    public function setMainText($mainText)
    {
        $this->main_text = $mainText;

        return $this;
    }

    /**
     * Get mainText
     *
     * @return string
     */
    public function getMainText()
    {
        return $this->main_text;
    }

    /**
     * Set publishedState
     *
     * @param boolean $publishedState
     *
     * @return ELearning
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
     * Set archivedState
     *
     * @param boolean $archivedState
     *
     * @return ELearning
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
     * Set viewNumber
     *
     * @param integer $viewNumber
     *
     * @return ELearning
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
     * Set publicationDatetime
     *
     * @param \DateTime $publicationDatetime
     *
     * @return ELearning
     */
    public function setPublicationDatetime($publicationDatetime)
    {
        $this->publication_datetime = $publicationDatetime;

        return $this;
    }

    /**
     * Get publicationDatetime
     *
     * @return \DateTime
     */
    public function getPublicationDatetime()
    {
        return $this->publication_datetime;
    }

    /**
     * Set viewerAuthorizationType
     *
     * @param string $viewerAuthorizationType
     *
     * @return ELearning
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
     * @return ELearning
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
     * @return ELearning
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
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return ELearning
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
     * Add mediaContent
     *
     * @param \AdminBundle\Entity\ELearningMediaContent $mediaContent
     *
     * @return ELearning
     */
    public function addMediaContent(\AdminBundle\Entity\ELearningMediaContent $mediaContent)
    {
        $this->media_contents[] = $mediaContent;

        return $this;
    }

    /**
     * Remove mediaContent
     *
     * @param \AdminBundle\Entity\ELearningMediaContent $mediaContent
     */
    public function removeMediaContent(\AdminBundle\Entity\ELearningMediaContent $mediaContent)
    {
        $this->media_contents->removeElement($mediaContent);
    }

    /**
     * Get mediaContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMediaContents()
    {
        return $this->media_contents;
    }

    /**
     * Add quizContent
     *
     * @param \AdminBundle\Entity\ELearningQuizContent $quizContent
     *
     * @return ELearning
     */
    public function addQuizContent(\AdminBundle\Entity\ELearningQuizContent $quizContent)
    {
        $this->quiz_contents[] = $quizContent;

        return $this;
    }

    /**
     * Remove quizContent
     *
     * @param \AdminBundle\Entity\ELearningQuizContent $quizContent
     */
    public function removeQuizContent(\AdminBundle\Entity\ELearningQuizContent $quizContent)
    {
        $this->quiz_contents->removeElement($quizContent);
    }

    /**
     * Get quizContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuizContents()
    {
        return $this->quiz_contents;
    }

    /**
     * Add buttonContent
     *
     * @param \AdminBundle\Entity\ELearningButtonContent $buttonContent
     *
     * @return ELearning
     */
    public function addButtonContent(\AdminBundle\Entity\ELearningButtonContent $buttonContent)
    {
        $this->button_contents[] = $buttonContent;

        return $this;
    }

    /**
     * Remove buttonContent
     *
     * @param \AdminBundle\Entity\ELearningButtonContent $buttonContent
     */
    public function removeButtonContent(\AdminBundle\Entity\ELearningButtonContent $buttonContent)
    {
        $this->button_contents->removeElement($buttonContent);
    }

    /**
     * Get buttonContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getButtonContents()
    {
        return $this->button_contents;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ELearning
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
     * @return ELearning
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
}
