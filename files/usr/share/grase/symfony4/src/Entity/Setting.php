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
     * @ORM\Column(name="setting", type="string", length=30, nullable=false)
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
    public const USERNAME_LENGTH = 'usernameLength';

    public const AUTO_CREATE_GROUP = 'autocreategroup';
    public const AUTO_CREATE_PASSWORD = 'autocreatepassword';
    public const SUPPORT_CONTACT_LINK = 'supportContactLink';
    public const SUPPORT_CONTACT_NAME = 'supportContactName';
    public const WEBSITE_LINK = 'websiteLink';
    public const WEBSITE_NAME = 'websiteName';

    public const NETWORK_LAST_CHANGED = 'lastnetworkconf';
    public const NETWORK_LAN_INTERFACE = 'networkLanInterface';
    public const NETWORK_LAN_IP = 'networkLanIP';
    public const NETWORK_LAN_MASK = 'networkLanMask';
    public const NETWORK_WAN_INTERFACE = 'networkWanInterface';
    public const NETWORK_OPENDNS_BOGUS_NX = 'networkOpenDNSBogusNX'; // Boolean to add the OpenDNS Bogus NX records
    public const NETWORK_DNS_SERVERS = 'networkDNSServers';
    public const NETWORK_BOGUS_NX = 'networkBogusNX'; // IP addresses that are bogus NX records, so should be converted to a NX record

    public const BOOLEAN_SETTINGS = [
        self::NETWORK_OPENDNS_BOGUS_NX,
    ];

    public const STRING_SETTINGS = [
        self::AUTO_CREATE_GROUP,
        self::AUTO_CREATE_PASSWORD,
        self::SUPPORT_CONTACT_LINK,
        self::SUPPORT_CONTACT_NAME,
        self::WEBSITE_LINK,
        self::WEBSITE_NAME,
        self::NETWORK_WAN_INTERFACE,
        self::NETWORK_LAN_INTERFACE,
        self::NETWORK_LAN_IP,
        self::NETWORK_LAN_MASK,
    ];

    public const NUMERIC_SETTINGS = [
        self::USERNAME_LENGTH,
        self::PASSWORD_LENGTH,
    ];

    public const ARRAY_SETTINGS = [
        self::NETWORK_BOGUS_NX,
        self::NETWORK_DNS_SERVERS,
        self::MB_OPTIONS,
        self::TIME_OPTIONS,
    ];


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
     * @return mixed
     */
    public function getValue()
    {
        return json_decode($this->getRawValue());
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->setRawValue(json_encode($value));
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setRawValue($value)
    {
        $this->value = $value;
    }
}
