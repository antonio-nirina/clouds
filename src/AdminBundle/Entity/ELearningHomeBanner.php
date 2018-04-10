<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ELearningHomeBanner
 *
 * @ORM\Table(name="e_learning_home_banner")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ELearningHomeBannerRepository")
 */
class ELearningHomeBanner
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="menu_name", type="string", length=255)
     */
    private $menuName;

    /**
     * @var string
     *
     * @ORM\Column(name="image_title", type="string", length=255)
     */
    private $imageTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="image_file", type="string", length=255)
     * @Assert\Image(
     *     maxSize = "8M",
     *     mimeTypes = {"image/jpeg", "image/png", "image/gif"}
     * )
     */
    private $imageFile;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\Program", inversedBy="elearning_banner_data")
     */
    private $program;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set menuName.
     *
     * @param string $menuName
     *
     * @return ELearningHomeBanner
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;

        return $this;
    }

    /**
     * Get menuName.
     *
     * @return string
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * Set imageTitle.
     *
     * @param string $imageTitle
     *
     * @return ELearningHomeBanner
     */
    public function setImageTitle($imageTitle)
    {
        $this->imageTitle = $imageTitle;

        return $this;
    }

    /**
     * Get imageTitle.
     *
     * @return string
     */
    public function getImageTitle()
    {
        return $this->imageTitle;
    }

    /**
     * Set imageFile.
     *
     * @param string $imageFile
     *
     * @return ELearningHomeBanner
     */
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Get imageFile.
     *
     * @return string
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }


    /**
     * Set eLearning.
     *
     * @param \AdminBundle\Entity\ELearning|null $eLearning
     *
     * @return ELearningHomeBanner
     */
    public function setELearning(\AdminBundle\Entity\ELearning $eLearning = null)
    {
        $this->e_learning = $eLearning;

        return $this;
    }

    /**
     * Get eLearning.
     *
     * @return \AdminBundle\Entity\ELearning|null
     */
    public function getELearning()
    {
        return $this->e_learning;
    }

    /**
     * Set program.
     *
     * @param \AdminBundle\Entity\Program|null $program
     *
     * @return ELearningHomeBanner
     */
    public function setProgram(\AdminBundle\Entity\Program $program = null)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program.
     *
     * @return \AdminBundle\Entity\Program|null
     */
    public function getProgram()
    {
        return $this->program;
    }
}
