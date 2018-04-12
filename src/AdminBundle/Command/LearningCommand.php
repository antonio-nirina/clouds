<?php
namespace AdminBundle\Command;

use AdminBundle\Entity\ELearningHomeBanner;
use AdminBundle\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AdminBundle\Component\Post\PostType;

/**
 * Command to be called for scheduled news post publication
 */
class LearningCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('app:create-elearning-banner')
            ->setDescription('E-learing banner.')
            ->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $currentProgram = $em->getRepository('AdminBundle\Entity\Program')->findOneBy(array('id' => 24));
        $banner = new ELearningHomeBanner();
        $banner->setMenuName("test");
        $banner->setImageTitle("test");
        $banner->setImageFile("test");
        $banner->setProgram($currentProgram);
        $em->persist($banner);
        $em->flush();
    }
}
