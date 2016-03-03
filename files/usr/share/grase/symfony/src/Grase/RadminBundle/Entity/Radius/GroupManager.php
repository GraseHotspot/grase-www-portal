<?php

namespace Grase\RadminBundle\Entity\Radius;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Grase\RadminBundle\Entity\Radius\Group;

/**
 * Class GroupManager
 * @package Grase\RadminBundle\Entity\Radius
 */
class GroupManager
{
    /**
     * Holds the Doctrine entity manager for database interaction
     * @var EntityManager
     */
    protected $em;

    /**
     * Holds the Symfony2 event dispatcher service
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repo;

    /**
     * The Fully-Qualified Class Name for our entity
     * @var string
     */
    protected $class;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
    }

    /**
     * @return Group
     */
    public function createGroup()
    {
        $class = $this->class;
        $group = new $class();

        return $group;
    }

    public function saveGroup(Group $group)
    {
        $this->em->persist($group);
        $this->em->flush();
        // And then we can dispatch an event if we desire
        //$this->dispatcher->dispatch('grase.group.save', new GroupEvent($group));
    }
}