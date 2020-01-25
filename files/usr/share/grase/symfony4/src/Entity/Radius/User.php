<?php

namespace App\Entity\Radius;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Util\DateIntervalEnhanced;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Entity\Radius\UserRepository")
 */
class User
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     *
     * @Groups({"user_get"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"user_get"})
     */
    private $comment;


    /**
     * @ORM\OneToMany(targetEntity="Check", mappedBy="user", fetch="EAGER")
     *
     * @var Check[]
     */
    private $radiusCheck;

    // TODO add $radiusReply

    /**
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="user", fetch="EAGER", cascade={"persist", "remove"})
     *
     * @Groups({"user_get"})
     */
    private $userGroups;

    /**
     * @ORM\OneToMany(targetEntity="Radacct", mappedBy="user", fetch="LAZY")
     */
    private $radiusAccounting;

    /**
     * Private variables for internal use
     */
    /** @var DateIntervalEnhanced */
    private $currentSessionTime = null;
    private $currentDataUsage = null;
    private $currentDataUsageIn = null;
    private $currentDataUsageOut = null;
    /** @var DateTime */
    private $lastLogout = null;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->radiusCheck      = new ArrayCollection();
        $this->userGroups       = new ArrayCollection();
        $this->radiusAccounting = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @Groups({"user_get"})
     *
     * @return null|string
     */
    public function getPassword()
    {
        if ($this->getPasswordCheck()) {
            return $this->getPasswordCheck()->getValue();
        }

        return null;
    }

    /**
     * @return Check
     */
    public function getPasswordCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Cleartext-Password'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }


    /**
     * Get the users time limit in a somewhat formatted way (good for displaying)
     *
     * @Groups({"user_get"})
     *
     * @return string
     */
    public function getTimeLimit()
    {
        if ($this->getTimeLimitCheck()) {
            $timeLimit = new DateIntervalEnhanced('PT' . $this->getTimeLimitCheck()->getValue() . 'S');

            return $timeLimit->recalculate()->format('%H:%I:%S');
        }

        return '∞';
    }

    /**
     * @return Check
     */
    public function getTimeLimitCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Max-All-Session'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }

    /**
     * Get the users time limit in Minutes, needed for editing users
     * @return float|int
     */
    public function getTimeLimitMinutes()
    {
        return $this->getTimeLimitSeconds() / 60;
    }

    /**
     * Get the users Time limit as seconds or null if there is no limit
     * @return string|null
     */
    public function getTimeLimitSeconds()
    {
        if ($this->getTimeLimitCheck()) {
            return $this->getTimeLimitCheck()->getValue();
        }

        return null;
    }

    /**
     * Get the users data limit as a mebibyte value, needed when editing users
     * @return float|int|null
     */
    public function getDataLimitMebibyte()
    {
        return null !== $this->getDataLimit() ? $this->getDataLimit() / 1024 / 1024 : null;
    }

    /**
     * Get the users Data limit as it's raw bytes value
     * @Groups({"user_get"})
     *
     * @return string | null
     */
    public function getDataLimit()
    {
        if ($this->getDataLimitCheck()) {
            return $this->getDataLimitCheck()->getValue();
        }

        return null;
    }

    /**
     * @return Check
     */
    public function getDataLimitCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Max-Octets'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }

    /**
     * @Groups({"user_get"})
     *
     * @return DateTime|null
     */
    public function getExpiry()
    {
        if ($this->getExpiryCheck()) {
            return new \DateTime($this->getExpiryCheck()->getValue());
        }

        return null;
    }

    /**
     * @return Check
     */
    public function getExpiryCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Expiration'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }

    /**
     * @return Check
     */
    public function getExpireAfterCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", Check::GRASE_EXPIRE_AFTER));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }

    /**
     * @return string|null
     */
    public function getExpireAfter()
    {
        if ($this->getExpireAfterCheck()) {
            return $this->getExpireAfterCheck()->getValue();
        }

        return null;
    }



    /**
     * Add radiuscheck
     *
     * @param Check $radiuscheck
     *
     * @return User
     */
    public function addRadiuscheck(Check $radiuscheck)
    {
        $this->radiusCheck[] = $radiuscheck;

        return $this;
    }

    /**
     * Remove radiuscheck
     *
     * @param Check $radiuscheck
     */
    public function removeRadiuscheck(Check $radiuscheck)
    {
        $this->radiusCheck->removeElement($radiuscheck);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Add userGroups
     *
     * @param UserGroup $userGroups
     *
     * @return User
     */
    public function addUserGroup(UserGroup $userGroups)
    {
        $this->userGroups[] = $userGroups;

        return $this;
    }

    /**
     * Remove userGroups
     *
     * @param UserGroup $userGroups
     */
    public function removeUserGroup(UserGroup $userGroups)
    {
        $this->userGroups->removeElement($userGroups);
    }

    /**
     * This shouldn't be needed as it's another object we can handle outside of here
     *
     * @param Group $group
     */
    /*public function setPrimaryGroup(Group $group)
    {
        /** @var UserGroup $primaryUserGroup *//*
        $primaryUserGroup = $this->getUserGroups()->first();
        $primaryUserGroup->setGroup($group);
    }*/

    /**
     * @return string of all user group names
     */
    public function getAllUserGroupsNames()
    {
        $groupnames = [];
        /** @var UserGroup $usergroup */
        foreach ($this->getUserGroups() as $usergroup) {
            $groupnames[] = $usergroup->getGroup()->getName();
        }

        return implode(',', $groupnames);
    }

    /**
     * Get userGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }

    /**
     * Get primary group name
     *
     * @return string
     */
    public function getPrimaryGroupName()
    {
        /** @var UserGroup $primaryGroup */
        $primaryGroup = $this->getUserGroups()->first();

        return $primaryGroup->getGroup()->getName();
    }

    /**
     * @return Group|null
     */
    public function getPrimaryGroup()
    {
        $primaryUserGroup = $this->getPrimaryUserGroup();

        return $primaryUserGroup ? $primaryUserGroup->getGroup() : null;
    }

    /**
     * @return UserGroup|null
     */
    public function getPrimaryUserGroup()
    {
        return $this->getUserGroups()->first() ?? null;
    }

    /**
     * Add radiusAccounting
     *
     * @param Radacct $radiusAccounting
     *
     * @return User
     */
    public function addRadiusAccounting(Radacct $radiusAccounting)
    {
        $this->radiusAccounting[] = $radiusAccounting;

        return $this;
    }

    /**
     * Remove radiusAccounting
     *
     * @param Radacct $radiusAccounting
     */
    public function removeRadiusAccounting(Radacct $radiusAccounting)
    {
        $this->radiusAccounting->removeElement($radiusAccounting);
    }

    /**
     * @Groups({"user_get_2"})
     *
     * @return string
     */
    public function getTotalSessionTime()
    {
        if ($this->currentSessionTime === null) {
            $sum = 0;
            /** @var Radacct $radactt */
            foreach ($this->getRadiusAccounting() as $radactt) {
                $sum += $radactt->getAcctsessiontime();
            }

            $this->currentSessionTime = new DateIntervalEnhanced('PT' . $sum . 'S');
        }

        // TODO Is the formatting of this best left to a view?
        return $this->currentSessionTime->recalculate()->format('%H:%I:%S');
    }

    /**
     * Get radiusAccounting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRadiusAccounting()
    {
        return $this->radiusAccounting;
    }

    /**
     * @Groups({"user_get_2"})
     *
     * @return integer
     */
    public function getDataUsageTotal()
    {
        if ($this->currentDataUsage === null) {
            /** @var Radacct $radactt */
            foreach ($this->getRadiusAccounting() as $radactt) {
                $this->currentDataUsageOut += $radactt->getAcctoutputoctets();
                $this->currentDataUsageIn  += $radactt->getAcctinputoctets();
            }

            $this->currentDataUsage = $this->currentDataUsageIn + $this->currentDataUsageOut;
        }

        return $this->currentDataUsage;
    }

    /**
     * @param array $data
     */
    public function hydrateRadiusAccountingData($data)
    {
        $this->currentDataUsageIn  = $data['currentAcctInputOctets'];
        $this->currentDataUsageOut = $data['currentAcctOutputOctets'];
        $this->currentSessionTime  = new DateIntervalEnhanced('PT' . $data['currentAcctSessionTime'] . 'S');
        $this->currentDataUsage    = $this->currentDataUsageIn + $this->currentDataUsageOut;
        $this->lastLogout          = $data['lastLogout'] ? new DateTime($data['lastLogout']) : null;
    }

    /**
     * @return DateTime
     */
    public function getLastLogout()
    {
        return $this->lastLogout;
    }

    /**
     * Get all the Radius Check Entries
     * @return ArrayCollection
     */
    protected function getRadiuscheck()
    {
        return $this->radiusCheck;
    }
}
