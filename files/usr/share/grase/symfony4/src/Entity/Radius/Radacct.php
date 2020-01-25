<?php

namespace App\Entity\Radius;

use App\Util\DateIntervalEnhanced;
use Doctrine\ORM\Mapping as ORM;

/**
 * Radacct
 *
 * @ORM\Table(name="radacct",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="acctuniqueid", columns={"acctuniqueid"})
 *      },
 *     indexes={
 *          @ORM\Index(name="UserName", columns={"UserName"}),
 *          @ORM\Index(name="FramedIPAddress", columns={"FramedIPAddress"}),
 *          @ORM\Index(name="AcctSessionId", columns={"AcctSessionId"}),
 *          @ORM\Index(name="AcctStartTime", columns={"AcctStartTime"}),
 *          @ORM\Index(name="AcctStopTime", columns={"AcctStopTime"}),
 *          @ORM\Index(name="NASIPAddress", columns={"NASIPAddress"})
 *      })
 * @ORM\Entity(repositoryClass="App\Entity\Radius\RadacctRepository")
 */
class Radacct
{
    /**
     * @var int
     *
     * @ORM\Column(name="RadAcctId", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $radacctid;

    /**
     * @var string
     *
     * @ORM\Column(name="AcctSessionId", type="string", length=32, nullable=false)
     */
    private $acctsessionid;

    /**
     * @var string
     *
     * @ORM\Column(name="AcctUniqueId", type="string", length=32, nullable=true)
     */
    private $acctuniqueid;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="radiusAccounting")
     * @ORM\JoinColumn(name="UserName", referencedColumnName="username")
     */
    private $user;


    /**
     * @var string
     *
     * @ORM\Column(name="Groupname", type="string", length=64, nullable=false)
     */
    private $groupname;

    /**
     * @var string
     *
     * @ORM\Column(name="Realm", type="string", length=64, nullable=true)
     */
    private $realm;

    /**
     * @var string
     *
     * @ORM\Column(name="NASIPAddress", type="string", length=15, nullable=false)
     */
    private $nasipaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="NASPortId", type="string", length=32, nullable=true)
     */
    private $nasportid;

    /**
     * @var string
     *
     * @ORM\Column(name="NASPortType", type="string", length=32, nullable=true)
     */
    private $nasporttype;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="AcctStartTime", type="datetime", nullable=true)
     */
    private $acctstarttime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="acctupdatetime", type="datetime", nullable=true)
     */
    private $acctUpdateTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="AcctStopTime", type="datetime", nullable=true)
     */
    private $acctstoptime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="acctinterval", type="integer", length=12, nullable=true)
     */
    private $acctInterval;

    /**
     * @var int
     *
     * @ORM\Column(name="AcctSessionTime", type="integer", nullable=true)
     */
    private $acctsessiontime;

    /**
     * @var string
     *
     * @ORM\Column(name="AcctAuthentic", type="string", length=32, nullable=true)
     */
    private $acctauthentic;

    /**
     * @var string
     *
     * @ORM\Column(name="ConnectInfo_start", type="string", length=50, nullable=true)
     */
    private $connectinfoStart;

    /**
     * @var string
     *
     * @ORM\Column(name="ConnectInfo_stop", type="string", length=50, nullable=true)
     */
    private $connectinfoStop;

    /**
     * @var int
     *
     * @ORM\Column(name="AcctInputOctets", type="bigint", nullable=true)
     */
    private $acctinputoctets;

    /**
     * @var int
     *
     * @ORM\Column(name="AcctOutputOctets", type="bigint", nullable=true)
     */
    private $acctoutputoctets;

    /**
     * @var string
     *
     * @ORM\Column(name="CalledStationId", type="string", length=50, nullable=false)
     */
    private $calledstationid;

    /**
     * @var string
     *
     * @ORM\Column(name="CallingStationId", type="string", length=50, nullable=false)
     */
    private $callingstationid;

    /**
     * @var string
     *
     * @ORM\Column(name="AcctTerminateCause", type="string", length=32, nullable=false)
     */
    private $acctterminatecause;

    /**
     * @var string
     *
     * @ORM\Column(name="ServiceType", type="string", length=32, nullable=true)
     */
    private $servicetype;

    /**
     * @var string
     *
     * @ORM\Column(name="FramedProtocol", type="string", length=32, nullable=true)
     */
    private $framedprotocol;

    /**
     * @var string
     *
     * @ORM\Column(name="FramedIPAddress", type="string", length=15, nullable=false)
     */
    private $framedipaddress;

    /**
     * Get radacctid
     *
     * @return integer
     */
    public function getRadacctid()
    {
        return $this->radacctid;
    }

    /**
     * Set acctsessionid
     *
     * @param string $acctsessionid
     *
     * @return Radacct
     */
    public function setAcctsessionid($acctsessionid)
    {
        $this->acctsessionid = $acctsessionid;

        return $this;
    }

    /**
     * Get acctsessionid
     *
     * @return string
     */
    public function getAcctsessionid()
    {
        return $this->acctsessionid;
    }

    /**
     * Set acctuniqueid
     *
     * @param string $acctuniqueid
     *
     * @return Radacct
     */
    public function setAcctuniqueid($acctuniqueid)
    {
        $this->acctuniqueid = $acctuniqueid;

        return $this;
    }

    /**
     * Get acctuniqueid
     *
     * @return string
     */
    public function getAcctuniqueid()
    {
        return $this->acctuniqueid;
    }

    /**
     * Set groupname
     *
     * @param string $groupname
     *
     * @return Radacct
     */
    public function setGroupname($groupname)
    {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * Get groupname
     *
     * @return string
     */
    public function getGroupname()
    {
        return $this->groupname;
    }

    /**
     * Set realm
     *
     * @param string $realm
     *
     * @return Radacct
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Get realm
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Set nasipaddress
     *
     * @param string $nasipaddress
     *
     * @return Radacct
     */
    public function setNasipaddress($nasipaddress)
    {
        $this->nasipaddress = $nasipaddress;

        return $this;
    }

    /**
     * Get nasipaddress
     *
     * @return string
     */
    public function getNasipaddress()
    {
        return $this->nasipaddress;
    }

    /**
     * Set nasportid
     *
     * @param string $nasportid
     *
     * @return Radacct
     */
    public function setNasportid($nasportid)
    {
        $this->nasportid = $nasportid;

        return $this;
    }

    /**
     * Get nasportid
     *
     * @return string
     */
    public function getNasportid()
    {
        return $this->nasportid;
    }

    /**
     * Set nasporttype
     *
     * @param string $nasporttype
     *
     * @return Radacct
     */
    public function setNasporttype($nasporttype)
    {
        $this->nasporttype = $nasporttype;

        return $this;
    }

    /**
     * Get nasporttype
     *
     * @return string
     */
    public function getNasporttype()
    {
        return $this->nasporttype;
    }

    /**
     * Set acctstarttime
     *
     * @param \DateTime $acctstarttime
     *
     * @return Radacct
     */
    public function setAcctstarttime($acctstarttime)
    {
        $this->acctstarttime = $acctstarttime;

        return $this;
    }

    /**
     * Get acctstarttime
     *
     * @return \DateTime
     */
    public function getAcctstarttime()
    {
        return $this->acctstarttime;
    }

    /**
     * Set acctstoptime
     *
     * @param \DateTime $acctstoptime
     *
     * @return Radacct
     */
    public function setAcctstoptime($acctstoptime)
    {
        $this->acctstoptime = $acctstoptime;

        return $this;
    }

    /**
     * Get acctstoptime
     *
     * @return \DateTime
     */
    public function getAcctstoptime()
    {
        return $this->acctstoptime;
    }

    /**
     * Set acctsessiontime
     *
     * @param integer $acctsessiontime
     *
     * @return Radacct
     */
    public function setAcctsessiontime($acctsessiontime)
    {
        $this->acctsessiontime = $acctsessiontime;

        return $this;
    }

    /**
     * Get acctsessiontime
     *
     * @return integer
     */
    public function getAcctsessiontime()
    {
        return $this->acctsessiontime;
    }

    /**
     * Displayable Account Session Time
     * @return string
     *
     * @throws \Exception
     */
    public function getDisplayAccountSessionTime()
    {
        $seconds = $this->getAcctsessiontime() ?? 0;
        $acctSessionTime = new DateIntervalEnhanced('PT' . $seconds . 'S');

        return $acctSessionTime->recalculate()->format('%H:%I:%S');
    }

    /**
     * Set acctauthentic
     *
     * @param string $acctauthentic
     *
     * @return Radacct
     */
    public function setAcctauthentic($acctauthentic)
    {
        $this->acctauthentic = $acctauthentic;

        return $this;
    }

    /**
     * Get acctauthentic
     *
     * @return string
     */
    public function getAcctauthentic()
    {
        return $this->acctauthentic;
    }

    /**
     * Set connectinfoStart
     *
     * @param string $connectinfoStart
     *
     * @return Radacct
     */
    public function setConnectinfoStart($connectinfoStart)
    {
        $this->connectinfoStart = $connectinfoStart;

        return $this;
    }

    /**
     * Get connectinfoStart
     *
     * @return string
     */
    public function getConnectinfoStart()
    {
        return $this->connectinfoStart;
    }

    /**
     * Set connectinfoStop
     *
     * @param string $connectinfoStop
     *
     * @return Radacct
     */
    public function setConnectinfoStop($connectinfoStop)
    {
        $this->connectinfoStop = $connectinfoStop;

        return $this;
    }

    /**
     * Get connectinfoStop
     *
     * @return string
     */
    public function getConnectinfoStop()
    {
        return $this->connectinfoStop;
    }

    /**
     * Set acctinputoctets
     *
     * @param integer $acctinputoctets
     *
     * @return Radacct
     */
    public function setAcctinputoctets($acctinputoctets)
    {
        $this->acctinputoctets = $acctinputoctets;

        return $this;
    }

    /**
     * Get acctinputoctets
     *
     * @return integer
     */
    public function getAcctinputoctets()
    {
        return $this->acctinputoctets;
    }

    /**
     * Set acctoutputoctets
     *
     * @param integer $acctoutputoctets
     *
     * @return Radacct
     */
    public function setAcctoutputoctets($acctoutputoctets)
    {
        $this->acctoutputoctets = $acctoutputoctets;

        return $this;
    }

    /**
     * Get acctoutputoctets
     *
     * @return integer
     */
    public function getAcctoutputoctets()
    {
        return $this->acctoutputoctets;
    }

    /**
     * Get AcctTotalOctets
     * @return int
     */
    public function getAcctTotalOctets()
    {
        return $this->getAcctinputoctets() + $this->getAcctoutputoctets();
    }

    /**
     * Set calledstationid
     *
     * @param string $calledstationid
     *
     * @return Radacct
     */
    public function setCalledstationid($calledstationid)
    {
        $this->calledstationid = $calledstationid;

        return $this;
    }

    /**
     * Get calledstationid
     *
     * @return string
     */
    public function getCalledstationid()
    {
        return $this->calledstationid;
    }

    /**
     * Set callingstationid
     *
     * @param string $callingstationid
     *
     * @return Radacct
     */
    public function setCallingstationid($callingstationid)
    {
        $this->callingstationid = $callingstationid;

        return $this;
    }

    /**
     * Get callingstationid
     *
     * @return string
     */
    public function getCallingstationid()
    {
        return $this->callingstationid;
    }

    /**
     * Set acctterminatecause
     *
     * @param string $acctterminatecause
     *
     * @return Radacct
     */
    public function setAcctterminatecause($acctterminatecause)
    {
        $this->acctterminatecause = $acctterminatecause;

        return $this;
    }

    /**
     * Get acctterminatecause
     *
     * @return string
     */
    public function getAcctterminatecause()
    {
        return $this->acctterminatecause;
    }

    /**
     * Set servicetype
     *
     * @param string $servicetype
     *
     * @return Radacct
     */
    public function setServicetype($servicetype)
    {
        $this->servicetype = $servicetype;

        return $this;
    }

    /**
     * Get servicetype
     *
     * @return string
     */
    public function getServicetype()
    {
        return $this->servicetype;
    }

    /**
     * Set framedprotocol
     *
     * @param string $framedprotocol
     *
     * @return Radacct
     */
    public function setFramedprotocol($framedprotocol)
    {
        $this->framedprotocol = $framedprotocol;

        return $this;
    }

    /**
     * Get framedprotocol
     *
     * @return string
     */
    public function getFramedprotocol()
    {
        return $this->framedprotocol;
    }

    /**
     * Set framedipaddress
     *
     * @param string $framedipaddress
     *
     * @return Radacct
     */
    public function setFramedipaddress($framedipaddress)
    {
        $this->framedipaddress = $framedipaddress;

        return $this;
    }

    /**
     * Get framedipaddress
     *
     * @return string
     */
    public function getFramedipaddress()
    {
        return $this->framedipaddress;
    }

    /**
     * Set user
     *
     * @param \App\Entity\Radius\User $user
     *
     * @return Radacct
     */
    public function setUser(\App\Entity\Radius\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\Radius\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
