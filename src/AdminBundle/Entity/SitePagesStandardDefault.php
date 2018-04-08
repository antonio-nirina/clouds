<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SitePagesStandardDefaultRepository")
 * @ORM\Table(name="site_pages_standard_default")
 */
class SitePagesStandardDefault
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="page_name", type="string", nullable=true)
     */
    private $page_name;
    
    /**
     * @ORM\Column(type="array")
     */
    private $options;

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
     * Set options
     *
     * @param boolean $options
     *
     * @return SitePagesStandardDefault
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
    
    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set pageName
     *
     * @param string $pageName
     *
     * @return SitePagesStandardDefault
     */
    public function setPageName($pageName)
    {
        $this->page_name = $pageName;

        return $this;
    }
    
    /**
     * Get pageName
     *
     * @param string $pageName
     *
     * @return SitePagesStandardDefault
     */
    public function getPageName()
    {
        return $this->page_name;
    }
}
