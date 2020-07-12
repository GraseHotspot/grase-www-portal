<?php

namespace App\Util;

use App\Util\SystemInformation\NetworkInterface;

/**
 * Utilities that directly talk to the system in some way (network information etc)
 *
 * Put it here so we can Mock it easily if required
 */
class SystemUtils
{
    /**
     * Array of network interfaces present on the system
     *
     * @var NetworkInterface[]
     */
    private $networkInterfaces;

    /**
     * Find all the NICs that are suitable as Hotspot LAN's. That is ones without an IP address (as Coova Chilli makes a tun on it)
     *
     * @return NetworkInterface[]
     */
    public function getPotentialLanNetworkInterfaces()
    {
        $this->populateNetworkInterfaces();

        // Find the networkInterfaces that are suitable for using as a LAN NIC (no current IP, no gateway)
        // TODO ensure we filter out the current WAN, and if the current LAN and WAN are the same, throw an error or null one out?
        $potentialLanInterfaces = [];
        foreach ($this->networkInterfaces as $networkInterface) {
            if ($networkInterface->ipaddress === null) {
                $potentialLanInterfaces[$networkInterface->iface] = $networkInterface;
            }
        }

        return $potentialLanInterfaces;
    }

    /**
     * Find all the NICs that are suitable as Hotspot WAN's. That is one with a gateway
     *
     * @return NetworkInterface[]
     */
    public function getPotentialWanNetworkInterfaces()
    {
        $this->populateNetworkInterfaces();

        // Find the networkInterfaces that are suitable for a WAN
        $potentialWanInterfaces = [];
        foreach ($this->networkInterfaces as $networkInterface) {
            if ($networkInterface->gateway) {
                $potentialWanInterfaces[$networkInterface->iface] = $networkInterface;
            }
        }

        return $potentialWanInterfaces;
    }

    /**
     * Populate $this->networkInterfaces so we don't have to run getNetworkInterfaces multiple times
     */
    private function populateNetworkInterfaces()
    {
        if (empty($this->networkInterfaces)) {
            $this->networkInterfaces = $this->getNetworkInterfaces();
        }
    }

    /**
     * Find all network interfaces on the system, filter out ones we never want, ensure we get a
     * NetworkInterface object for each
     *
     * @return NetworkInterface[]
     */
    private function getNetworkInterfaces()
    {
        // /sys/class/net/enp2s0/
        $networkInterfaces = [];
        // The network names we care about the most are en*, wl*, br*. We don't care about veth. We should filter out tun* unless looking for our own LAN
        foreach (glob('/sys/class/net/*') as $sysNetworkInterfaceName) {
            if (strstr($sysNetworkInterfaceName, '/veth')) {
                continue;
            }
            if (strstr($sysNetworkInterfaceName, '/tun')) {
                continue;
            }
            if (strstr($sysNetworkInterfaceName, '/lo')) {
                continue;
            }
            if (strstr($sysNetworkInterfaceName, '/virbr')) {
                continue;
            }
            if (strstr($sysNetworkInterfaceName, '/ztc')) {
                continue;
            }
            if (strstr($sysNetworkInterfaceName, '/docker')) {
                continue;
            }
            echo "$sysNetworkInterfaceName\n";
            $networkInterface = $this->getInterfaceDetails($sysNetworkInterfaceName);
            $networkInterfaces[] = $networkInterface;
        }

        return $networkInterfaces;
    }

    /**
     * Lookup as much inforamtion about a network interface as possible and populate a NetworkInterface object
     *
     * @param $sysNetworkInterfacename
     *
     * @return NetworkInterface
     */
    private function getInterfaceDetails($sysNetworkInterfacename)
    {
        $interface = new NetworkInterface();
        $parts = preg_split('/\//', $sysNetworkInterfacename);
        $interface->iface = end($parts);
        $interface->mac = trim(file_get_contents($sysNetworkInterfacename . '/address'));
        [$interface->ipaddress, $interface->netmask] = SystemInformation::discoverIPAddressAndNetmask($interface->iface);
        $interface->gateway = SystemInformation::getInterfaceGateway($interface->iface);

        return $interface;
    }
}
