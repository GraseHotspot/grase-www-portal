<?php

namespace App\Entity\Radius;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mtotacct
 *
 * @ORM\Table(name="mtotacct")
 * @ORM\Entity
 */
class AccountingMonthly
{
    /**
     * @var int
     *
     * @ORM\Column(name="MTotAcctId", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="UserName", referencedColumnName="username")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="NASIPAddress", type="string", length=15, nullable=false)
     */
    private $nasIpAddress;

    /**
     * @var int
     *
     * @ORM\Column(name="ConnTotDuration", type="integer", nullable=false)
     */
    private $totalDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="ConnMaxDuration", type="integer", nullable=false)
     */
    private $maxDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="ConnMinDuration", type="integer", nullable=false)
     */
    private $minDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="InputOctets", type="bigint", nullable=false)
     */
    private $inputOctets;

    /**
     * @var int
     *
     * @ORM\Column(name="OutputOctets", type="bigint", nullable=false)
     */
    private $outputOctets;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="AcctDate", type="date", nullable=false)
     */
    private $month;

    /**
     * @var int
     *
     * @ORM\Column(name="ConnNum", type="integer", nullable=false)
     */
    private $numberOfConnections;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getNasIpAddress()
    {
        return $this->nasIpAddress;
    }

    /**
     * @return int
     */
    public function getTotalDuration()
    {
        return $this->totalDuration;
    }

    /**
     * @return int
     */
    public function getMaxDuration()
    {
        return $this->maxDuration;
    }

    /**
     * @return int
     */
    public function getMinDuration()
    {
        return $this->minDuration;
    }

    /**
     * @return int
     */
    public function getInputOctets()
    {
        return $this->inputOctets;
    }

    /**
     * @return int
     */
    public function getOutputOctets()
    {
        return $this->outputOctets;
    }

    /**
     * @return DateTime
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return int
     */
    public function getNumberOfConnections()
    {
        return $this->numberOfConnections;
    }
}
