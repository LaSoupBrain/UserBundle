<?php

namespace Dtw\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Dtw\UserBundle\Entity\User;


/**
 * This class is for the Entities listener and permit to add changes by the doctrine lifecycle workflow.
 *
 * @package Dtw\UserBundle\EventListener
 *
 * @author Richard
 */
class EntitiesListener
{
    /**
     * Insert current date to createdAt for worker.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     * @author Richard Soliven
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof User) {
            $entity->setCreatedAt(new \DateTime());
        }
    }

    /**
     * This function will put the current Datetime every time you update an entity.
     *
     * @param LifecycleEventArgs $args
     * @throws \Exception
     *
     * @author Richard
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof User) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}