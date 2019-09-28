<?php

namespace App\Entity\Radius;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Entity\Radius\Group;

/**
 * Class GroupManager
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
     * GroupManager constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface   $em
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->repo = $em->getRepository(Group::class);
    }

    /**
     * @return Group
     */
    public function createGroup()
    {
        return new Group();
    }

    /**
     * @param \App\Entity\Radius\Group $group
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveGroup(Group $group)
    {
        $this->em->persist($group);
        $this->em->flush();
        // And then we can dispatch an event if we desire
        //$this->dispatcher->dispatch('grase.group.save', new GroupEvent($group));
    }
}
