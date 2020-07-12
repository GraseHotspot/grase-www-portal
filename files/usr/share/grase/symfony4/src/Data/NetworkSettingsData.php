<?php

namespace App\Data;

use App\Entity\Setting;
use App\Util\SettingsUtils;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Data Object for Network Settings Forms
 */
class NetworkSettingsData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Ip(version="4")
     *
     * @var string
     */
    public $lanIpAddress;

    /**
     * @Assert\NotBlank()
     *
     * @\App\Validator\Constraints\SubnetMask()
     *
     * @var string
     */
    public $lanNetworkMask;

    /**
     * // TODO add warnings when blank
     *
     * @var string
     */
    public $lanNetworkInterface;

    /**
     * // TODO add warnings when blank
     *
     * @var string
     */
    public $wanNetworkInterface;

    /**
     * @Assert\NotBlank()
     *
     * @var array
     *
     * @Assert\Ip(version="4")
     * @Assert\Collection()
     */
    public $dnsServers;

    /**
     * @var array
     *
     * @Assert\Collection()
     */
    public $bogusNxDomains;

    /**
     * @var SettingsUtils
     */
    private $settingsUtils;

    /**
     * NetworkSettingsData constructor.
     *
     * @param SettingsUtils $settingsUtils
     */
    public function __construct(SettingsUtils $settingsUtils)
    {
        $this->settingsUtils = $settingsUtils;
        $this->load();
    }

    /**
     * Load data from Settings into object
     */
    public function load()
    {
        $this->lanIpAddress = $this->settingsUtils->getSettingValue(Setting::NETWORK_LAN_IP);
        $this->lanNetworkMask = $this->settingsUtils->getSettingValue(Setting::NETWORK_LAN_MASK);
        $this->lanNetworkInterface = $this->settingsUtils->getSettingValue(Setting::NETWORK_LAN_INTERFACE);
        $this->wanNetworkInterface = $this->settingsUtils->getSettingValue(Setting::NETWORK_WAN_INTERFACE);
        $this->dnsServers = $this->settingsUtils->getSettingValue(Setting::NETWORK_DNS_SERVERS);
        $this->bogusNxDomains = $this->settingsUtils->getSettingValue(Setting::NETWORK_BOGUS_NX);
    }
}
