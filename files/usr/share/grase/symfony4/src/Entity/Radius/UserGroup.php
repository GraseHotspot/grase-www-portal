<?php

namespace App\Entity\Radius;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * UserGroup
 * @ORM\Table(name="radusergroup")
 * @ORM\Entity(repositoryClass="App\Entity\Radius\UserGroupRepository")
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
     * @Groups({"user_get"})
     */
    private $group;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user_get"})
     */
    private $priority = 1;

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
     * @param \App\Entity\Radius\User $user
     * @return UserGroup
     */
    public function setUser(\App\Entity\Radius\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\Radius\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set group
     *
     * @param \App\Entity\Radius\Group $group
     * @return UserGroup
     */
    public function setGroup(\App\Entity\Radius\Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \App\Entity\Radius\Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
