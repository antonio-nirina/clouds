<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SiteFormFieldSettingRepository")
 * @ORM\Table(name="site_form_field_setting")
 */
class SiteFormFieldSetting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $field_type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mandatory;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $field_order;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $in_row;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $additional_data;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\SiteFormSetting", inversedBy="site_form_field_settings")
     */
    private $site_form_setting;

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
     * Set fieldType
     *
     * @param string $fieldType
     *
     * @return SiteFormFieldSetting
     */
    public function setFieldType($fieldType)
    {
        $this->field_type = $fieldType;

        return $this;
    }

    /**
     * Get fieldType
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->field_type;
    }

    /**
     * Set mandatory
     *
     * @param boolean $mandatory
     *
     * @return SiteFormFieldSetting
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set repsonse
     *
     * @param string $repsonse
     *
     * @return SiteFormFieldSetting
     */
    public function setRepsonse($repsonse)
    {
        $this->repsonse = $repsonse;

        return $this;
    }

    /**
     * Get repsonse
     *
     * @return string
     */
    public function getRepsonse()
    {
        return $this->repsonse;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return SiteFormFieldSetting
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return SiteFormFieldSetting
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return SiteFormFieldSetting
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set additionalData
     *
     * @param array $additionalData
     *
     * @return SiteFormFieldSetting
     */
    public function setAdditionalData($additionalData)
    {
        $this->additional_data = $additionalData;

        return $this;
    }

    /**
     * Get additionalData
     *
     * @return array
     */
    public function getAdditionalData()
    {
        return $this->additional_data;
    }

    /**
     * Set response
     *
     * @param string $response
     *
     * @return SiteFormFieldSetting
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set siteFormSetting
     *
     * @param \AdminBundle\Entity\SiteFormSetting $siteFormSetting
     *
     * @return SiteFormFieldSetting
     */
    public function setSiteFormSetting(\AdminBundle\Entity\SiteFormSetting $siteFormSetting = null)
    {
        $this->site_form_setting = $siteFormSetting;

        return $this;
    }

    /**
     * Get siteFormSetting
     *
     * @return \AdminBundle\Entity\SiteFormSetting
     */
    public function getSiteFormSetting()
    {
        return $this->site_form_setting;
    }

    /**
     * Set fieldOrder
     *
     * @param integer $fieldOrder
     *
     * @return SiteFormFieldSetting
     */
    public function setFieldOrder($fieldOrder)
    {
        $this->field_order = $fieldOrder;

        return $this;
    }

    /**
     * Get fieldOrder
     *
     * @return integer
     */
    public function getFieldOrder()
    {
        return $this->field_order;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return SiteFormFieldSetting
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set inRow
     *
     * @param integer $inRow
     *
     * @return SiteFormFieldSetting
     */
    public function setInRow($inRow)
    {
        $this->in_row = $inRow;

        return $this;
    }

    /**
     * Get inRow
     *
     * @return integer
     */
    public function getInRow()
    {
        return $this->in_row;
    }
}
