<?php

namespace AdminBundle\DoctrineListener;

use AdminBundle\Entity\Program;
use Doctrine\Common\EventSubscriber;
// use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ProgramListener implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $object = $args->getObject();

        if (! $object instanceof Program) {
            return;
        }

        $object->setDateLastUpdate(new \DateTime);
    }
}
