<?php
namespace AdminBundle\Manager;

use AdminBundle\Entity\Program;
use AdminBundle\Entity\HomePagePost;
use AdminBundle\Component\Post\PostType;
use Doctrine\ORM\EntityManager;

class HomePagePostEditoManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createEdito(Program $program, HomePagePost $home_page_post, $flush = true)
    {
        $home_page_post->setPostType(PostType::EDITO)
            ->setProgram($program);
        $this->em->persist($home_page_post);
        $program->addHomePagePost($home_page_post);

        if (true == $flush) {
            $this->flush();
        }
    }

    public function flush()
    {
        $this->em->flush();
    }
}