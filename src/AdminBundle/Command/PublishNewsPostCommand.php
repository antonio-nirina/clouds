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
        $current_date = new \DateTime('now');
        $current_date->setTime($current_date->format('H'), $current_date->format('i'), 0);
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $to_publish_news_post_list = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findToPublishByTypeAndProgrammedPublicationDatetime(PostType::NEWS_POST, $current_date);
        $news_post_manager = $this->getContainer()->get('AdminBundle\Manager\NewsPostManager');
        foreach ($to_publish_news_post_list as $news_post) {
            $news_post_manager->definePublishedState($news_post, true, $current_date, false);
        }
        $news_post_manager->flush();
    }
}