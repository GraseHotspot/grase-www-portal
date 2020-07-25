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
     * @var array
     *
     * @Assert\NotBlank()
     * @Assert\All({
     *      @Assert\Ip(version="4")
     * })
     */
    public $dnsServers;

    /**
     * @var array
     *
     * @Assert\All({
     *      @Assert\Ip(version="4")
     * })
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

    /**
     * Save the network settings back. This should never be called it the object validation has failed
     */
    public function save()
    {
        $this->settingsUtils->updateSettingByName(Setting::NETWORK_LAN_IP, $this->lanIpAddress);
        $this->settingsUtils->updateSettingByName(Setting::NETWORK_LAN_MASK, $this->lanNetworkMask);
        $this->settingsUtils->updateSettingByName(Setting::NETWORK_LAN_INTERFACE, $this->lanNetworkInterface);
        $this->settingsUtils->updateSettingByName(Setting::NETWORK_WAN_INTERFACE, $this->wanNetworkInterface);
        $this->settingsUtils->updateSettingByName(Setting::NETWORK_DNS_SERVERS, $this->dnsServers);
        $this->settingsUtils->updateSettingByName(Setting::NETWORK_BOGUS_NX, $this->bogusNxDomains);
    }
}
