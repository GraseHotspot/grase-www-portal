<?php

namespace Grase\RadminBundle\Entity\Radius;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
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
     * @ORM\OneToMany(targetEntity="Check", mappedBy="username")
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
}
