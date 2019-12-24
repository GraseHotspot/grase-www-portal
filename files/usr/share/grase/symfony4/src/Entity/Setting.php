<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="setting", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=2000, nullable=false)
     */
    private $value;

    public const MB_OPTIONS = 'mbOptions';
    public const TIME_OPTIONS = 'timeOptions';
    public const PASSWORD_LENGTH = 'passwordLength';
    public const AUTO_CREATE_GROUP = 'autocreategroup';
    public const SUPPORT_CONTACT_LINK = 'supportContactLink';
    public const SUPPORT_CONTACT_NAME = 'supportContactName';

    /**
     * Setting constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
