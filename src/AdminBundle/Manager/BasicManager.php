<?php
namespace AdminBundle\Manager;

use Doctrine\ORM\EntityManager;

/**
 * Holding common functions for manager
 */
class BasicManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * flush on current EntityManager
     */
    public function flush()
    {
        $this->em->flush();
    }
}
