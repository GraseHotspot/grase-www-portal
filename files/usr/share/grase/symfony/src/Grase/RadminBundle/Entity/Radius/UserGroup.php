<?php

namespace Grase\RadminBundle\Entity\Radius;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * UserGroup
 * @ApiResource
 * @ORM\Table(name="radusergroup")
 * @ORM\Entity(repositoryClass="Grase\RadminBundle\Entity\Radius\UserGroupRepository")
 */
class UserGroup
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userGroups")
     * @ORM\JoinColumn(name="UserName", referencedColumnName="username")
     */
    private $user;

    /**
     * @var string
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="userGroups")
     * @ORM\JoinColumn(name="GroupName", referencedColumnName="id")
     */
    private $group;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * Set priority
     *
     * @param integer $priority
     * @return UserGroup
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set user
     *
     * @param \Grase\RadminBundle\Entity\Radius\User $user
     * @return UserGroup
     */
    public function setUser(\Grase\RadminBundle\Entity\Radius\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Grase\RadminBundle\Entity\Radius\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set group
     *
     * @param \Grase\RadminBundle\Entity\Radius\Group $group
     * @return UserGroup
     */
    public function setGroup(\Grase\RadminBundle\Entity\Radius\Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Grase\RadminBundle\Entity\Radius\Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
