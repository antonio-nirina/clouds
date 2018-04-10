<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09/04/2018
 * Time: 17:46
 */

namespace AdminBundle\Manager;

use AdminBundle\Entity\ELearningHomeBanner;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Filesystem\Filesystem;

class HomeBannerElearningManager
{
    private $em;
    private $container;
    private $filesystem;

    public function __construct(EntityManager $em, Container $container, Filesystem $filesystem)
    {
        $this->em = $em;
        $this->container = $container;
        $this->filesystem = $filesystem;
    }

    public function save()
    {
        return $this->em->flush();
    }


}