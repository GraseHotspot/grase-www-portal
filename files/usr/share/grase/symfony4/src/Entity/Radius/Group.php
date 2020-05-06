<?php

namespace App\Entity\Radius;

use App\Validator\Constraints as GraseAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Group
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="App\Entity\Radius\GroupRepository")
 */
class Group
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Groups({"user_get"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="Expiry", type="string", length=100, nullable=true)
     *
     * @GraseAssert\StrToTime
     *
     * @Groups({"user_get"})
     */
    private $expiry;

    /**
     * @var string
     *
     * @ORM\Column(name="ExpireAfter", type="string", length=100, nullable=true)
     *
     * @GraseAssert\StrToTime
     *
     * @Groups({"user_get"})
     */
    private $expireAfter;

    /**
     * @var int
     *
     * @ORM\Column(name="MaxOctets", type="integer", nullable=true)
     *
     * @Groups({"user_get"})
     */
    private $maxOctets;

    /**
     * @var int
     *
     * @ORM\Column(name="MaxSeconds", type="integer", nullable=true)
     *
     * @Groups({"user_get"})
     */
    private $maxSeconds;

    /**
     * @var string
     *
     * @ORM\Column(name="Comment", type="string", length=300, nullable=true)
     *
     * @Groups({"user_get"})
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="lastUpdated", type="datetime")
     * @ORM\Version
     */
    private $lastUpdated;

    /**
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="group", fetch="EAGER")
     */
    private $userGroups;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userGroups
     *
     * @param \App\Entity\Radius\UserGroup $userGroups
     *
     * @return Group
     */
    public function addUsergroup(\App\Entity\Radius\UserGroup $userGroups)
    {
        $this->userGroups[] = $userGroups;

        return $this;
    }

    /**
     * Remove userGroups
     *
     * @param \App\Entity\Radius\UserGroup $userGroups
     */
    public function removeUsergroup(\App\Entity\Radius\UserGroup $userGroups)
    {
        $this->userGroups->removeElement($userGroups);
    }

    /**
     * Get userGroups
     *
     * @return UserGroup[]|\Doctrine\Common\Collections\Collection
     */
    public function getUsergroups()
    {
        return $this->userGroups;
    }

    /**
     * Set expiry
     *
     * @param string $expiry
     *
     * @return Group
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Get expiry
     *
     * @return string
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set expireAfter
     *
     * @param string $expireAfter
     *
     * @return Group
     */
    public function setExpireAfter($expireAfter)
    {
        $this->expireAfter = $expireAfter;

        return $this;
    }

    /**
     * Get expireAfter
     *
     * @return string
     */
    public function getExpireAfter()
    {
        return $this->expireAfter;
    }

    /**
     * Set maxOctets
     *
     * @param int $maxOctets
     *
     * @return Group
     */
    public function setMaxOctets($maxOctets)
    {
        $this->maxOctets = $maxOctets;

        return $this;
    }

    /**
     * Get maxOctets
     *
     * @return int
     */
    public function getMaxOctets()
    {
        return $this->maxOctets;
    }

    /**
     * Set maxSeconds
     *
     * @param int $maxSeconds
     *
     * @return Group
     */
    public function setMaxSeconds($maxSeconds)
    {
        $this->maxSeconds = $maxSeconds;

        return $this;
    }

    /**
     * Get maxSeconds
     *
     * @return int
     */
    public function getMaxSeconds()
    {
        return $this->maxSeconds;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Group
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set lastUpdated
     *
     * @param \DateTime $lastUpdated
     *
     * @return Group
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
