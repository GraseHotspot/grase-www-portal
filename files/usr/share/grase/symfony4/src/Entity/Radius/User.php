<?php

namespace App\Entity\Radius;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Util\DateIntervalEnhanced;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ApiResource(attributes={"normalization_context"={"groups"={"user_get"}}})
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Entity\Radius\UserRepository")
 */
class User
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @Groups({"user_get"})
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Groups({"user_get"})
     */
    private $comment;


    /**
     * @ORM\OneToMany(targetEntity="Check", mappedBy="user", fetch="EAGER")
     */
    private $radiusCheck;

    /**
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="user", fetch="EAGER")
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
    private $totalSessionTime = null;
    private $totalDataUsage = null;

    public function __construct()
    {
        $this->radiusCheck      = new ArrayCollection();
        $this->userGroups       = new ArrayCollection();
        $this->radiusAccounting = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @Groups({"user_get"})
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

    public function getRadiuscheck()
    {
        return $this->radiusCheck;
    }

    /**
     * @Groups({"user_get"})
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
     * @Groups({"user_get"})
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
     * @Groups({"user_get"})
     * @return string
     */
    public function getExpiry()
    {
        if ($this->getExpiryCheck()) {
            return new \DateTime($this->getExpiryCheck()->getValue());
        }

        return null;
    }

    /**
     * Add radiuscheck
     *
     * @param Check $radiuscheck
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
     * Add radiusAccounting
     *
     * @param Radacct $radiusAccounting
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
     * @return string
     */
    public function getTotalSessionTime()
    {
        if ($this->totalSessionTime === null) {
            $sum = 0;
            /** @var Radacct $radactt */
            foreach ($this->getRadiusAccounting() as $radactt) {
                $sum += $radactt->getAcctsessiontime();
            }

            $this->totalSessionTime = new DateIntervalEnhanced('PT' . $sum . 'S');
        }
        // TODO Is the formatting of this best left to a view?
        return $this->totalSessionTime->recalculate()->format('%H:%I:%S');
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
     * @return integer
     */
    public function getDataUsage()
    {
        if ($this->totalDataUsage === null) {
            $sum = 0;
            /** @var Radacct $radactt */
            foreach ($this->getRadiusAccounting() as $radactt) {
                $sum += $radactt->getAcctTotalOctets();
            }

            $this->totalDataUsage = $sum;
        }

        return $this->totalDataUsage;
    }

    /**
     * @return Check
     */
    private function getTimeLimitCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Max-All-Session'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }

    /**
     * @return Check
     */
    private function getDataLimitCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Max-Octets'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }

    /**
     * @return Check
     */
    private function getExpiryCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Expiration'));

        return $this->getRadiuscheck()->matching($criteria)->first();
    }
}
