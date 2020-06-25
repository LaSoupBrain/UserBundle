<?php

namespace Dtw\UserBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;

/**
 * This class is used for all save and remove operation in all entity.
 *
 * @package Dtw\UserBundle\Utils
 *
 * @author Richard Soliven
 */
class DatabaseUtils
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * To save an entity in database.
     *
     * @param $entity
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return DatabaseUtils
     */
    public function save($entity): DatabaseUtils
    {
        try {
            $this->em->persist($entity);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the insertion of an entity.'
            );
        }

        return $this;
    }

    /**
     * To remove an entity in database.
     *
     * @param $entity
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return DatabaseUtils
     */
    public function remove($entity): DatabaseUtils
    {
        try {
            $this->em->remove($entity);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception(
                'An error occurred at the deletion of an entity.'
            );
        }

        return $this;
    }
}