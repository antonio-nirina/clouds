<?php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Traits\EntityTraits\ELearningContentTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="e_learning_quiz_content")
 */
class ELearningQuizContent
{
    use ELearningContentTrait;

    /**
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\SondagesQuizQuestionnaireInfos")
     */
    private $quiz;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ELearning", inversedBy="quiz_contents")
     */
    private $e_learning;

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
     * @return ELearningQuizContent
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
     * Set contentOrder
     *
     * @param integer $contentOrder
     *
     * @return ELearningQuizContent
     */
    public function setContentOrder($contentOrder)
    {
        $this->content_order = $contentOrder;

        return $this;
    }

    /**
     * Get contentOrder
     *
     * @return integer
     */
    public function getContentOrder()
    {
        return $this->content_order;
    }

    /**
     * Set quiz
     *
     * @param \AdminBundle\Entity\SondagesQuizQuestionnaireInfos $quiz
     *
     * @return ELearningQuizContent
     */
    public function setQuiz(\AdminBundle\Entity\SondagesQuizQuestionnaireInfos $quiz = null)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get quiz
     *
     * @return \AdminBundle\Entity\SondagesQuizQuestionnaireInfos
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * Set eLearning
     *
     * @param \AdminBundle\Entity\ELearning $eLearning
     *
     * @return ELearningQuizContent
     */
    public function setELearning(\AdminBundle\Entity\ELearning $eLearning = null)
    {
        $this->e_learning = $eLearning;

        return $this;
    }

    /**
     * Get eLearning
     *
     * @return \AdminBundle\Entity\ELearning
     */
    public function getELearning()
    {
        return $this->e_learning;
    }
}
