<?php

namespace App\Entity\Radius;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * Radius reply attributes
 *
 * @ORM\Table(name="radreply", indexes={@Index(name="userattribute", columns={"UserName", "Attribute", "op"})})
 * @ORM\Entity()
 */
class Reply
{
    public const REPLY_MESSAGE = 'Reply-Message';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="radiusReply")
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
    private $op = ':=';

    /**
     * @var string
     *
     * @ORM\Column(name="Value", type="string", length=255)
     */
    private $value;

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
     * Set attribute
     *
     * @param string $attribute
     *
     * @return Reply
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
     *
     * @return Reply
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
     *
     * @return Reply
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
     * @param User $user
     *
     * @return Reply
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
