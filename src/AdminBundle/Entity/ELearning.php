<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="text")
     */
    private $main_text;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ELearningContent", mappedBy="e_learning")
     */
    private $contents;

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
     * Constructor
     */
    public function __construct()
    {
        $this->contents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add content
     *
     * @param \AdminBundle\Entity\ELearningContent $content
     *
     * @return ELearning
     */
    public function addContent(\AdminBundle\Entity\ELearningContent $content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \AdminBundle\Entity\ELearningContent $content
     */
    public function removeContent(\AdminBundle\Entity\ELearningContent $content)
    {
        $this->contents->removeElement($content);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContents()
    {
        return $this->contents;
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
}
