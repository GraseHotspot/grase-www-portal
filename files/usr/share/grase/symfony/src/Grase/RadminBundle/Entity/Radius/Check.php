<?php

namespace Grase\RadminBundle\Entity\Radius;

use Doctrine\ORM\Mapping as ORM;


/**
 * Check
 *
 * @ORM\Table(name="radius.radcheck")
 * @ORM\Entity(repositoryClass="Grase\RadminBundle\Entity\Radius\CheckRepository")
 */
class Check
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="radiuscheck")
     * @ORM\JoinColumn(name="UserName", referencedColumnName="username", nullable=false)
     */
    private $user;


    /**
     * @var string
     *
     * @ORM\Column(name="Attribute", type="string", length=64)
     */
    private $attribute;

    /**
     * @var string
     *
     * @ORM\Column(name="op", type="string", length=2)
     */
    private $op;

    /**
     * @var string
     *
     * @ORM\Column(name="Value", type="string", length=255)
     */
    private $value;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set attribute
     *
     * @param string $attribute
     * @return Check
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return string 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set op
     *
     * @param string $op
     * @return Check
     */
    public function setOp($op)
    {
        $this->op = $op;

        return $this;
    }

    /**
     * Get op
     *
     * @return string 
     */
    public function getOp()
    {
        return $this->op;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Check
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \Grase\RadminBundle\Entity\Radius\User $user
     * @return Check
     */
    public function setUser(\Grase\RadminBundle\Entity\Radius\User $user = null)
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
}
