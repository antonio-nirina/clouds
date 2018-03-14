<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\AttributeOverrides({
 *  @ORM\AttributeOverride(name="username",
 *     column=@ORM\Column(
 *          name="username",
 *          type="string",
 *          length=180,
 *          nullable=true
 *     )
 * ),
 *  @ORM\AttributeOverride(name="usernameCanonical",
 *     column=@ORM\Column(
 *          name="username_canonical",
 *          type="string",
 *          length=180,
 *          unique=false,
 *          nullable=true
 *     )
 * ),
 *  @ORM\AttributeOverride(name="email",
 *     column=@ORM\Column(
 *          name="email",
 *          type="string",
 *          length=180,
 *          nullable=true,
 *     )
 * ),
 *  @ORM\AttributeOverride(name="emailCanonical",
 *     column=@ORM\Column(
 *          name="email_canonical",
 *          type="string",
 *          length=180,
 *          unique=false,
 *          nullable=true
 *     )
 * ),
 *  @ORM\AttributeOverride(name="enabled",
 *     column=@ORM\Column(
 *          name="enabled",
 *          type="boolean",
 *          nullable=true
 *     )
 * ),
 *  @ORM\AttributeOverride(name="password",
 *     column=@ORM\Column(
 *          name="password",
 *          type="string",
 *          nullable=true
 *     )
 * ),
 * })
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="temporary_pwd", type="boolean")
     */
    protected $temporaryPwd;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\ProgramUser", mappedBy="app_user")
     */
    protected $program_user;
	
	/**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ResultatsSondagesQuiz", mappedBy="user")
     */
    private $resultats_sondages_quiz;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $contact_information;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Length(max=5)
     */
    protected $civility;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $pro_email;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(max=25)
     */
    protected $phone;
    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    protected $mobile_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $address_1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $address_2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $postal_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $city;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $temporary;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $customization;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $societe;

    /**
    * @Recaptcha\IsTrue
    */
    public $recaptcha;
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->resultats_sondages_quiz = new ArrayCollection();
    }

   
    /**
    * @ORM\PrePersist
    */
    public function initTemporaryPwd()
    {
        return $this->setTemporaryPwd(true);
    }
   
    /**
     * Set temporaryPwd
     *
     * @param boolean $temporaryPwd
     *
     * @return User
     */
    public function setTemporaryPwd($temporaryPwd)
    {
        $this->temporaryPwd = $temporaryPwd;

        return $this;
    }

    /**
     * Get temporaryPwd
     *
     * @return boolean
     */
    public function getTemporaryPwd()
    {
        return $this->temporaryPwd;
    }

    /**
     * Set programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return User
     */
    public function setProgramUser(\AdminBundle\Entity\ProgramUser $programUser = null)
    {
        $this->program_user = $programUser;

        return $this;
    }

    /**
     * Get programUser
     *
     * @return \AdminBundle\Entity\ProgramUser
     */
    public function getProgramUser()
    {
        return $this->program_user;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set contactInformation
     *
     * @param string $contactInformation
     *
     * @return User
     */
    public function setContactInformation($contactInformation)
    {
        $this->contact_information = $contactInformation;

        return $this;
    }

    /**
     * Get contactInformation
     *
     * @return string
     */
    public function getContactInformation()
    {
        return $this->contact_information;
    }

    /**
     * Set civility
     *
     * @param string $civility
     *
     * @return User
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;

        return $this;
    }

    /**
     * Get civility
     *
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     *
     * @return User
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobile_phone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobile_phone;
    }

    /**
     * Set address1
     *
     * @param string $address1
     *
     * @return User
     */
    public function setAddress1($address1)
    {
        $this->address_1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address_1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     *
     * @return User
     */
    public function setAddress2($address2)
    {
        $this->address_2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address_2;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return User
     */
    public function setPostalCode($postalCode)
    {
        $this->postal_code = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set temporary
     *
     * @param boolean $temporary
     *
     * @return User
     */
    public function setTemporary($temporary)
    {
        $this->temporary = $temporary;

        return $this;
    }

    /**
     * Get temporary
     *
     * @return boolean
     */
    public function getTemporary()
    {
        return $this->temporary;
    }

    /**
     * Set customization
     *
     * @param array $customization
     *
     * @return User
     */
    public function setCustomization($customization)
    {
        $this->customization = $customization;

        return $this;
    }

    /**
     * Get customization
     *
     * @return array
     */
    public function getCustomization()
    {
        return $this->customization;
    }

    /**
     * Set proEmail
     *
     * @param string $proEmail
     *
     * @return User
     */
    public function setProEmail($proEmail)
    {
        $this->pro_email = $proEmail;

        return $this;
    }

    /**
     * Get proEmail
     *
     * @return string
     */
    public function getProEmail()
    {
        return $this->pro_email;
    }

    /**
     * Set societe
     *
     * @param string $societe
     *
     * @return User
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return string
     */
    public function getSociete()
    {
        return $this->societe;
    }
	
	/**
     * Add resultatsSondagesQuiz
     *
     * @param \AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz
     *
     * @return ResultatsSondagesQuiz
     */
    public function addResultatsSondagesQuiz(\AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz)
    {
        $this->resultats_sondages_quiz[] = $resultatsSondagesQuiz;

        return $this;
    }

    /**
     * Remove resultatsSondagesQuiz
     *
     * @param \AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz
     */
    public function removeResultatsSondagesQuiz(\AdminBundle\Entity\ResultatsSondagesQuiz $resultatsSondagesQuiz)
    {
        $this->resultats_sondages_quiz->removeElement($resultatsSondagesQuiz);
    }

    /**
     * Get sondagesQuizQuestions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResultatsSondagesQuiz()
    {
        return $this->resultats_sondages_quiz;
    }

    public function setEmail($email){
        parent::setEmail($email);
        $this->setUsername($email);
    }

    
}
