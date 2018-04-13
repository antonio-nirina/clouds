<?php
namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AdminBundle\Component\Post\PostType;

/**
 * Command to be called for scheduled news post publication
 */
class PublishNewsPostCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('app:publish-news-post')
            ->setDescription('Publish news posts that reach their publication datetime.')
            ->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $currentDate = new \DateTime('now');
        $currentDate->setTime($currentDate->format('H'), $currentDate->format('i'), 0);
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $toPublishNewsPostList = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findToPublishByTypeAndProgrammedPublicationDatetime(PostType::NEWS_POST, $currentDate);
        $newsPostManager = $this->getContainer()->get('AdminBundle\Manager\NewsPostManager');
        foreach ($toPublishNewsPostList as $newsPost) {
            $newsPostManager->definePublishedState($newsPost, true, $currentDate, false);
        }
        $newsPostManager->flush();
    }
}
