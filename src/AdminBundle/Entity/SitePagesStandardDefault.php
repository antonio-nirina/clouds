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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
