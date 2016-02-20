<?php

namespace Grase\RadminBundle\Entity\Radius;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Grase\RadminBundle\Entity\Radius\Check;
use Grase\Util\DateIntervalEnhanced;

/**
 * User
 *
 * @ORM\Table(name="radius.users")
 * @ORM\Entity(repositoryClass="Grase\RadminBundle\Entity\Radius\UserRepository")
 */
class User
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $comment;


    /**
     * @ORM\OneToMany(targetEntity="Check", mappedBy="user", fetch="EAGER")
     */
    private $radiuscheck;

    public function __construct()
    {
        $this->radiuscheck = new ArrayCollection();
    }

    public function getRadiuscheck()
    {
        return $this->radiuscheck;
    }

    public function getUsername()
    {
        return $this->username;
    }

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

    public function getTimeLimit()
    {
        if ($this->getTimeLimitCheck()) {
            $timeLimit = new DateIntervalEnhanced('PT'. $this->getTimeLimitCheck()->getValue() . 'S');
            return $timeLimit->recalculate()->format('%H:%I:%S');
        }
        return null;
    }

    /**
     * @return Check
     */
    private function getTimeLimitCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Max-All-Session'));
        return $this->getRadiuscheck()->matching($criteria)->first();
    }

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
    private function getDataLimitCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Max-Octets'));
        return $this->getRadiuscheck()->matching($criteria)->first();
    }

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
    private function getExpiryCheck()
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("attribute", 'Expiration'));
        return $this->getRadiuscheck()->matching($criteria)->first();
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
     * Add radiuscheck
     *
     * @param \Grase\RadminBundle\Entity\Radius\Check $radiuscheck
     * @return User
     */
    public function addRadiuscheck(\Grase\RadminBundle\Entity\Radius\Check $radiuscheck)
    {
        $this->radiuscheck[] = $radiuscheck;

        return $this;
    }

    /**
     * Remove radiuscheck
     *
     * @param \Grase\RadminBundle\Entity\Radius\Check $radiuscheck
     */
    public function removeRadiuscheck(\Grase\RadminBundle\Entity\Radius\Check $radiuscheck)
    {
        $this->radiuscheck->removeElement($radiuscheck);
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


}
