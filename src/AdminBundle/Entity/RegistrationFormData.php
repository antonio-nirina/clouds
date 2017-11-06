<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="registration_form_data")
 */
class RegistrationFormData
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(
     *     maxSize = "8M",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"}
     * )
     */
    private $header_image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $header_message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $introduction_text_title;

    /**
     * @ORM\Column(type="text")
     */
    private $introduction_text_content;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program", mappedBy="registration_form_data")
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
     * Set headerImage
     *
     * @param string $headerImage
     *
     * @return RegistrationFormData
     */
    public function setHeaderImage($headerImage)
    {
        $this->header_image = $headerImage;

        return $this;
    }

    /**
     * Get headerImage
     *
     * @return string
     */
    public function getHeaderImage()
    {
        return $this->header_image;
    }

    /**
     * Set headerMessage
     *
     * @param string $headerMessage
     *
     * @return RegistrationFormData
     */
    public function setHeaderMessage($headerMessage)
    {
        $this->header_message = $headerMessage;

        return $this;
    }

    /**
     * Get headerMessage
     *
     * @return string
     */
    public function getHeaderMessage()
    {
        return $this->header_message;
    }

    /**
     * Set introductionTextTitle
     *
     * @param string $introductionTextTitle
     *
     * @return RegistrationFormData
     */
    public function setIntroductionTextTitle($introductionTextTitle)
    {
        $this->introduction_text_title = $introductionTextTitle;

        return $this;
    }

    /**
     * Get introductionTextTitle
     *
     * @return string
     */
    public function getIntroductionTextTitle()
    {
        return $this->introduction_text_title;
    }

    /**
     * Set introductionTextContent
     *
     * @param string $introductionTextContent
     *
     * @return RegistrationFormData
     */
    public function setIntroductionTextContent($introductionTextContent)
    {
        $this->introduction_text_content = $introductionTextContent;

        return $this;
    }

    /**
     * Get introductionTextContent
     *
     * @return string
     */
    public function getIntroductionTextContent()
    {
        return $this->introduction_text_content;
    }

    /**
     * Set program
     *
     * @param \AdminBundle\Entity\Program $program
     *
     * @return RegistrationFormData
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
