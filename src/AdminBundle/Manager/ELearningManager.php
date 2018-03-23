<?php
namespace AdminBundle\Manager;

use AdminBundle\Manager\BasicManager;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\ELearning;
use AdminBundle\Component\Submission\SubmissionType;

class ELearningManager extends BasicManager
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    /**
     * Create ELearning
     *
     * @param ELearning $eLearning
     * @param $submission_type
     * @param bool $flush
     *
     * @return bool
     */
    public function create(ELearning $eLearning, $submission_type, $flush = true)
    {
        if (SubmissionType::PUBLISH == $submission_type) {
            $eLearning = $this->prepareForPublish($eLearning);
        }
        $this->em->persist($eLearning);

        if ($flush) {
            $this->flush();
        }

        return true;
    }

    /**
     * Prepare ELearning for publication
     *
     * @param ELearning $eLearning
     *
     * @return ELearning
     */
    public function prepareForPublish(ELearning $eLearning)
    {
        $eLearning->setPublishedState(true)
            ->setPublicationDatetime(new \DateTime('now'));

        return $eLearning;
    }
}