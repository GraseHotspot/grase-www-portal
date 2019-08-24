<?php

namespace App\Entity\Radius;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Entity\Radius\Group;

/**
 * Class GroupManager
 * @package App\Entity\Radius
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

    public function saveGroup(Group $group)
    {
        $this->em->persist($group);
        $this->em->flush();
        // And then we can dispatch an event if we desire
        //$this->dispatcher->dispatch('grase.group.save', new GroupEvent($group));
    }
}
