<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="client")
 * @ORM\HasLifecycleCallbacks()
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="society", type="string", length=255)
     */
    protected $society;

    /**
     * @ORM\Column(name="postal_address", type="string", length=255)
     */
    protected $postal_address;

    /**
     * @ORM\Column(name="po_first_name", type="string", length=255)
     */
    protected $po_first_name;

    /**
     * @ORM\Column(name="po_last_name", type="string", length=255)
     */
    protected $po_last_name;

    /**
     * @ORM\Column(name="po_post", type="string", length=255)
     */
    protected $po_post;

    /**
     * @ORM\Column(name="po_email", type="string", length=255)
     */
    protected $po_email;

    /**
     * @ORM\Column(name="po_phone", type="string", length=255)
     */
    protected $po_phone;

    /**
     * @ORM\Column(name="KYC_contact", nullable=true, type="string", length=255)
     */
    protected $KYC_contact;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

    /**
     * @ORM\PrePersist
     */
    public function initDate()
    {
        return $this->setDate(new \DateTime());
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
     * Set society
     *
     * @param string $society
     *
     * @return Client
     */
    public function setSociety($society)
    {
        $this->society = $society;

        return $this;
    }

    /**
     * Get society
     *
     * @return string
     */
    public function getSociety()
    {
        return $this->society;
    }

    /**
     * Set postalAddress
     *
     * @param string $postalAddress
     *
     * @return Client
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
     * Set poFirstName
     *
     * @param string $poFirstName
     *
     * @return Client
     */
    public function setPoFirstName($poFirstName)
    {
        $this->po_first_name = $poFirstName;

        return $this;
    }

    /**
     * Get poFirstName
     *
     * @return string
     */
    public function getPoFirstName()
    {
        return $this->poFirstName;
    }

    /**
     * Set poLastName
     *
     * @param string $poLastName
     *
     * @return Client
     */
    public function setPoLastName($poLastName)
    {
        $this->po_last_name = $poLastName;

        return $this;
    }

    /**
     * Get poLastName
     *
     * @return string
     */
    public function getPoLastName()
    {
        return $this->po_last_name;
    }

    /**
     * Set poPost
     *
     * @param string $poPost
     *
     * @return Client
     */
    public function setPoPost($poPost)
    {
        $this->po_post = $poPost;

        return $this;
    }

    /**
     * Get poPost
     *
     * @return string
     */
    public function getPoPost()
    {
        return $this->po_post;
    }

    /**
     * Set poEmail
     *
     * @param string $poEmail
     *
     * @return Client
     */
    public function setPoEmail($poEmail)
    {
        $this->po_email = $poEmail;

        return $this;
    }

    /**
     * Get poEmail
     *
     * @return string
     */
    public function getPoEmail()
    {
        return $this->po_email;
    }

    /**
     * Set poPhone
     *
     * @param string $poPhone
     *
     * @return Client
     */
    public function setPoPhone($poPhone)
    {
        $this->po_phone = $poPhone;

        return $this;
    }

    /**
     * Get poPhone
     *
     * @return string
     */
    public function getPoPhone()
    {
        return $this->po_phone;
    }

    /**
     * Set kYCContact
     *
     * @param string $kYCContact
     *
     * @return Client
     */
    public function setKYCContact($kYCContact)
    {
        $this->KYC_contact = $kYCContact;

        return $this;
    }

    /**
     * Get kYCContact
     *
     * @return string
     */
    public function getKYCContact()
    {
        return $this->KYC_contact;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Client
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
