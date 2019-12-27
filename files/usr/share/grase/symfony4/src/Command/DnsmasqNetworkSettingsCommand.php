<?php

namespace App\Command;

use App\Entity\Setting;
use App\Util\SettingsUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use Twig\Template;

/**
 * This command renders out the dnsmasq network settings file, which is used by dnsmasq and
 * coova-chilli to get the settings from the Grase hotspot database
 */
class DnsmasqNetworkSettingsCommand extends Command
{
    protected static $defaultName = 'grase:dnsmasqNetworkSettingsConfig';

    /** @var Environment */
    private $twig;

    /**
     * @var SettingsUtils
     */
    private $settingsUtils;

    /**
     * DnsmasqNetworkSettingsCommand constructor.
     *
     * @param SettingsUtils $settingsUtils
     * @param Environment   $twig
     */
    public function __construct(SettingsUtils $settingsUtils, Environment $twig)
    {
        parent::__construct();
        $this->twig = $twig;
        $this->settingsUtils = $settingsUtils;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $settingsUtils = $this->settingsUtils;
        $lanIP = $settingsUtils->getSettingValue(Setting::NETWORK_LAN_IP);
        $lanNetmask = $settingsUtils->getSettingValue(Setting::NETWORK_LAN_MASK);

        $lanNetworkIP = long2ip(ip2long($lanIP) & ip2long($lanNetmask));

        $output->write($this->twig->render(
            'dnsmasqNetworkSettings.txt.twig',
            [
                'lanIP' => $lanIP,
                'lanInterface' => $settingsUtils->getSettingValue(Setting::NETWORK_LAN_INTERFACE),
                'wanInterface' => $settingsUtils->getSettingValue(Setting::NETWORK_WAN_INTERFACE),
                'lanNetwork' => $lanNetworkIP,
                'lanNetmask' => $lanNetmask,
                'dnsServers' => $settingsUtils->getSettingValue(Setting::NETWORK_DNS_SERVERS),
                'bogusNX' => $settingsUtils->getSettingValue(Setting::NETWORK_BOGUS_NX),
                'lastChangedTimestamp' => $settingsUtils->getSettingValue(Setting::NETWORK_LAST_CHANGED),
            ]
        ));
    }
}
