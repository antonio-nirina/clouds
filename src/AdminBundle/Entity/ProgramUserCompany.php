<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ProgramUserCompanyRepository")
 * @ORM\Table(name="program_user_company")
 */
class ProgramUserCompany
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postal_address;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $customization;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\ProgramUser", mappedBy="program_user_company", cascade={"persist"})
     */
    private $program_users;

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
     * Set name
     *
     * @param string $name
     *
     * @return ProgramUserCompany
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
     * Set postalAddress
     *
     * @param string $postalAddress
     *
     * @return ProgramUserCompany
     */
    public function setPostalAddress($postalAddress)
    {
        $this->postal_address = $postalAddress;

        return $this;
    }

    /**
     * Get postalAddress
     *
     * @return string
     */
    public function getPostalAddress()
    {
        return $this->postal_address;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return ProgramUserCompany
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
     * @return ProgramUserCompany
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
     * Set country
     *
     * @param string $country
     *
     * @return ProgramUserCompany
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set customization
     *
     * @param array $customization
     *
     * @return ProgramUserCompany
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
     * Constructor
     */
    public function __construct()
    {
        $this->programUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     *
     * @return ProgramUserCompany
     */
    public function addProgramUser(\AdminBundle\Entity\ProgramUser $programUser)
    {
        $this->programUsers[] = $programUser;

        return $this;
    }

    /**
     * Remove programUser
     *
     * @param \AdminBundle\Entity\ProgramUser $programUser
     */
    public function removeProgramUser(\AdminBundle\Entity\ProgramUser $programUser)
    {
        $this->program_users->removeElement($programUser);
    }

    /**
     * Get programUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgramUsers()
    {
        return $this->program_users;
    }
}
