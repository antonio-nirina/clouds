<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="com_email_template")
 */
class ComEmailTemplate
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $template_model;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Program")
     */
    private $program;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $last_edit;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $last_edit_user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Length(max=10)
     */
    private $logo_alignment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $action_button_text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $action_button_url;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $action_button_background_color;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $action_button_text_color;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $email_color;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    private $background_color;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footer_list_state;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footer_company_state;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footer_unsubscribing_state;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footer_contact_state;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ComEmailTemplateContent", mappedBy="template")
     * @Assert\Valid()
     */
    private $contents;
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
     * Set logo
     *
     * @param string $logo
     *
     * @return ComEmailTemplate
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set logoAlignment
     *
     * @param string $logoAlignment
     *
     * @return ComEmailTemplate
     */
    public function setLogoAlignment($logoAlignment)
    {
        $this->logo_alignment = $logoAlignment;

        return $this;
    }

    /**
     * Get logoAlignment
     *
     * @return string
     */
    public function getLogoAlignment()
    {
        return $this->logo_alignment;
    }

    /**
     * Set actionButtonText
     *
     * @param string $actionButtonText
     *
     * @return ComEmailTemplate
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
     * Set actionButtonUrl
     *
     * @param string $actionButtonUrl
     *
     * @return ComEmailTemplate
     */
    public function setActionButtonUrl($actionButtonUrl)
    {
        $this->action_button_url = $actionButtonUrl;

        return $this;
    }

    /**
     * Get actionButtonUrl
     *
     * @return string
     */
    public function getActionButtonUrl()
    {
        return $this->action_button_url;
    }

    /**
     * Set actionButtonBackgroundColor
     *
     * @param string $actionButtonBackgroundColor
     *
     * @return ComEmailTemplate
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
     * Set actionButtonTextColor
     *
     * @param string $actionButtonTextColor
     *
     * @return ComEmailTemplate
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
     * Set footerListState
     *
     * @param boolean $footerListState
     *
     * @return ComEmailTemplate
     */
    public function setFooterListState($footerListState)
    {
        $this->footer_list_state = $footerListState;

        return $this;
    }

    /**
     * Get footerListState
     *
     * @return boolean
     */
    public function getFooterListState()
    {
        return $this->footer_list_state;
    }

    /**
     * Set footerCompanyState
     *
     * @param boolean $footerCompanyState
     *
     * @return ComEmailTemplate
     */
    public function setFooterCompanyState($footerCompanyState)
    {
        $this->footer_company_state = $footerCompanyState;

        return $this;
    }

    /**
     * Get footerCompanyState
     *
     * @return boolean
     */
    public function getFooterCompanyState()
    {
        return $this->footer_company_state;
    }

    /**
     * Set footerUnsubscribingState
     *
     * @param boolean $footerUnsubscribingState
     *
     * @return ComEmailTemplate
     */
    public function setFooterUnsubscribingState($footerUnsubscribingState)
    {
        $this->footer_unsubscribing_state = $footerUnsubscribingState;

        return $this;
    }

    /**
     * Get footerUnsubscribingState
     *
     * @return boolean
     */
    public function getFooterUnsubscribingState()
    {
        return $this->footer_unsubscribing_state;
    }

    /**
     * Set footerContactState
     *
     * @param boolean $footerContactState
     *
     * @return ComEmailTemplate
     */
    public function setFooterContactState($footerContactState)
    {
        $this->footer_contact_state = $footerContactState;

        return $this;
    }

    /**
     * Get footerContactState
     *
     * @return boolean
     */
    public function getFooterContactState()
    {
        return $this->footer_contact_state;
    }

    /**
     * Add content
     *
     * @param \AdminBundle\Entity\ComEmailTemplateContent $content
     *
     * @return ComEmailTemplate
     */
    public function addContent(\AdminBundle\Entity\ComEmailTemplateContent $content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \AdminBundle\Entity\ComEmailTemplateContent $content
     */
    public function removeContent(\AdminBundle\Entity\ComEmailTemplateContent $content)
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
     * Set name
     *
     * @param string $name
     *
     * @return ComEmailTemplate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastEdit
     *
     * @param \DateTime $lastEdit
     *
     * @return ComEmailTemplate
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
     * @return ComEmailTemplate
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
     * Set emailColor
     *
     * @param string $emailColor
     *
     * @return ComEmailTemplate
     */
    public function setEmailColor($emailColor)
    {
        $this->email_color = $emailColor;

        return $this;
    }

    /**
     * Get emailColor
     *
     * @return string
     */
    public function getEmailColor()
    {
        return $this->email_color;
    }

    /**
     * Set backgroundColor
     *
     * @param string $backgroundColor
     *
     * @return ComEmailTemplate
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->background_color = $backgroundColor;

        return $this;
    }

    /**
     * Get backgroundColor
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->background_color;
    }

    /**
     * Set templateModel
     *
     * @param string $templateModel
     *
     * @return ComEmailTemplate
     */
    public function setTemplateModel($templateModel)
    {
        $this->template_model = $templateModel;

        return $this;
    }

    /**
     * Get templateModel
     *
     * @return string
     */
    public function getTemplateModel()
    {
        return $this->template_model;
    }

    /**
     * Set lastEditUser
     *
     * @param \UserBundle\Entity\User $lastEditUser
     *
     * @return ComEmailTemplate
     */
    public function setLastEditUser(\UserBundle\Entity\User $lastEditUser = null)
    {
        $this->last_edit_user = $lastEditUser;

        return $this;
    }

    /**
     * Get lastEditUser
     *
     * @return \UserBundle\Entity\User
     */
    public function getLastEditUser()
    {
        return $this->last_edit_user;
    }
}
