<?php
namespace AdminBundle\Manager;

use AdminBundle\Manager\BasicManager;
use AdminBundle\Entity\ELearning;
use AdminBundle\Component\Submission\SubmissionType;
use AdminBundle\Entity\ELearningMediaContent;
use AdminBundle\Entity\ELearningQuizContent;
use AdminBundle\Entity\ELearningButtonContent;
use AdminBundle\Component\ELearning\ELearningContentType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ELearningManager extends BasicManager
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        // parent::__construct($em);
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Create ELearning
     *
     * @param ELearning $e_learning
     * @param $submission_type
     * @param bool $flush
     *
     * @return bool
     */
    public function create(ELearning $e_learning, $submission_type, $flush = true)
    {
        if (SubmissionType::PUBLISH == $submission_type) {
            $e_learning = $this->prepareForPublish($e_learning);
        }
        $this->saveDataForCreate($e_learning);

        if ($flush) {
            $this->flush();
        }

        return true;
    }

    /**
     * Prepare ELearning for publication
     *
     * @param ELearning $e_learning
     *
     * @return ELearning
     */
    public function prepareForPublish(ELearning $e_learning)
    {
        $e_learning->setPublishedState(true)
            ->setPublicationDatetime(new \DateTime('now'));

        return $e_learning;
    }

    /**
     * Save all e-learning data with media, quiz and button data
     *
     * @param ELearning $e_learning
     */
    public function saveDataForCreate(ELearning $e_learning)
    {
        $media_content_list = $e_learning->getMediaContents();
        foreach ($media_content_list as $media_content) {
            $this->saveMediaContentData($e_learning, $media_content);
        }

        $quiz_content_list = $e_learning->getQuizContents();
        foreach ($quiz_content_list as $quiz_content) {
            $this->saveQuizContentData($e_learning, $quiz_content);
        }

        $button_content_list = $e_learning->getButtonContents();
        foreach ($button_content_list as $button_content) {
            $this->saveButtonContentData($e_learning, $button_content);
        }

        $this->em->persist($e_learning);
    }

    /**
     * Save media content data
     * And upload files
     *
     * @param ELearning $e_learning
     * @param ELearningMediaContent $e_learning_media_content
     */
    public function saveMediaContentData(ELearning $e_learning, ELearningMediaContent $e_learning_media_content)
    {
        $e_learning_media_content->setELearning($e_learning);
        if (ELearningContentType::MEDIA_IMAGE_GALLERY == $e_learning_media_content->getContentType()
            && !empty($e_learning_media_content->getImages())
        ) {
            foreach ($e_learning_media_content->getImages() as $image) {
                if (!is_null($image->getImageFile())) {
                    $image_file = $image->getImageFile();
                    $image_file->move(
                        $this->container->getParameter('e_learning_media_gallery_image_dir'),
                        $image_file->getClientOriginalName()
                    );
                    $image->setImageFile($image_file->getClientOriginalName());
                }
                $this->em->persist($image);
            }
        }

        if (ELearningContentType::MEDIA_DOCUMENT == $e_learning_media_content->getContentType()) {
            if (!is_null($e_learning_media_content->getAssociatedFile())) {
                $associated_file = $e_learning_media_content->getAssociatedFile();
                $associated_file->move(
                    $this->container->getParameter('e_learning_media_document_dir'),
                    $associated_file->getClientOriginalName()
                );
                $e_learning_media_content->setAssociatedFile($associated_file->getClientOriginalName());
            }
        }

        $this->em->persist($e_learning_media_content);
    }

    /**
     * Save quiz content data
     *
     * @param ELearning $e_learning
     * @param ELearningQuizContent $e_learning_quiz_content
     */
    public function saveQuizContentData(ELearning $e_learning, ELearningQuizContent $e_learning_quiz_content)
    {
        $e_learning_quiz_content->setELearning($e_learning);
        $this->em->persist($e_learning_quiz_content);
    }

    /**
     * Save button content data
     *
     * @param ELearning $e_learning
     * @param ELearningButtonContent $e_learning_button_content
     */
    public function saveButtonContentData(ELearning $e_learning, ELearningButtonContent $e_learning_button_content)
    {
        $e_learning_button_content->setELearning($e_learning);
        $this->em->persist($e_learning_button_content);
    }
}