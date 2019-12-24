<?php

namespace App\Entity\Radius\Radpostauth;

use Doctrine\ORM\Mapping as ORM;

/**
 * Radpostauth
 *
 * @ORM\Table(name="radpostauth")
 * @ORM\Entity
 */
class RadPostAuth
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * Reference to the user, however we can't FK this due to it containing data that may not have
     * a valid user
     *
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, nullable=false)
     */
    private $username;

    /**
     * The password used for logging in
     * @TODO get this removed from the logs?
     *
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * This is the reply message sent back, e.g. Access-Accept, Access-Reject
     * @var string
     *
     * @ORM\Column(name="reply", type="string", length=32, nullable=false)
     */
    private $reply;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="authdate", type="datetime", nullable=false)
     */
    private $authDate;

    /**
     * The service login type, normally Framed-User, Administrative-User or Login-User
     *
     * @var string|null
     *
     * @ORM\Column(name="ServiceType", type="string", length=32, nullable=true)
     */
    private $serviceType;

    /**
     * The client IP address
     *
     * @var string|null
     *
     * @ORM\Column(name="FramedIPAddress", type="string", length=15, nullable=true)
     */
    private $framedIpAddress;
    /**
     * MAC address of the client logging in
     *
     * @var string|null
     *
     * @ORM\Column(name="CallingStationId", type="string", length=50, nullable=true)
     */
    private $callingStationId = 'NULL';

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getReply(): string
    {
        return $this->reply;
    }

    /**
     * @return \DateTime
     */
    public function getAuthDate(): \DateTime
    {
        return $this->authDate;
    }

    /**
     * @return string|null
     */
    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    /**
     * @return string|null
     */
    public function getFramedIpAddress(): ?string
    {
        return $this->framedIpAddress;
    }

    /**
     * @return string|null
     */
    public function getCallingStationId(): ?string
    {
        return $this->callingStationId;
    }
}
