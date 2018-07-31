<?php
namespace Grase;

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://grasehotspot.org/

    GRASE Hotspot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GRASE Hotspot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GRASE Hotspot.  If not, see <http://www.gnu.org/licenses/>.
*/

/* TODO: Check some of the code in here for origin */



use Grase\SystemInformation\CpuDevice;
use Grase\SystemInformation\HTTPD;
use Grase\SystemInformation\NetworkInterface;

class SystemInformation
{
    public $lan;
    public $wan;
    public $cpu;
    public $hostname;
    public $uptime;
    public $httpd;

    public function __construct()
    {
        $this->lan = new NetworkInterface();
        $this->wan = new NetworkInterface();
        $this->cpu = new CpuDevice(); // Only takes information for 1 core/processor
        $this->httpd = new HTTPD();

        // Load Settings in correct order
        $this->discoverLANInterface();
        $this->discoverWANInterface();
        $this->discoverHostname();
        $this->discoverUptime();
        $this->discoverCPU();
        $this->httpd();
    }

    public function httpd()
    {
        $this->httpd->software = $_SERVER['SERVER_SOFTWARE'];
        $this->httpd->gateway = PHP_SAPI;
    }

    private function discoverHostname()
    {
        if (function_exists('gethostname')) {
            $this->hostname = gethostname();
        } else {
            $this->hostname = php_uname('n');
        }
    }

    private function discoverLANInterface()
    {
        // Destination     Gateway         Genmask         Flags Metric Ref    Use Iface
        $details = preg_split("/\s+/", `/sbin/route -n |grep -v 10.64.6|grep tun`);
        $this->lan->netmask = $details[2] ?? null;
        $this->lan->iface = $details[7] ?? null;

        if ($this->lan->iface) {
            $this->lan->mac = $this->discoverMAC($this->lan->iface);
            list($this->lan->ipaddress, $this->lan->netmask) = $this->discoverIPAddressAndNetmask($this->lan->iface);
        }

    }

    private function CIDRtoMask($int) {
        return long2ip(-1 << (32 - (int)$int));
    }

    private function discoverWANInterface()
    {
        // Destination     Gateway         Genmask         Flags Metric Ref    Use Iface
        $lines = preg_split("/\n/", trim(`/sbin/route -n | sort -r |grep -v Kernel|grep -v Destination`));

        $details = preg_split("/\s+/", $lines[0]);
        $this->wan->iface = $details[7] ?? null;
        $details = preg_split("/\s+/", end($lines));
        $this->wan->gateway = $details[1] ?? null;

        $this->wan->mac = $this->discoverMAC($this->wan->iface);
        list($this->wan->ipaddress, $this->wan->netmask) = $this->discoverIPAddressAndNetmask($this->wan->iface);
        $this->discoverDNS($this->wan);

    }

    private function discoverDNS($ifaceobj)
    {
        $nameservers = preg_split("/\n/", trim(shell_exec("grep nameserver /etc/resolv.conf")));
        $ifaceobj->dns_primary = array_values(array_slice(preg_split("/\s+/", $nameservers[0], 2), -1))[0];
        if (isset($nameservers[1])) {
            $ifaceobj->dns_secondary = end(preg_split("/\s+/", $nameservers[1], 2));
        }

    }

    private function discoverIPAddressAndNetmask($iface)
    {
        if (!$iface) {
            return "";
        }
        $ipAndNetmask = shell_exec(
            //"ip -f inet addr show $iface | sed -En -e 's/.*inet ([0-9.]+).*/\\1/p'"
        "ip -f inet addr show $iface | awk '/inet / {print $2}'"
        );
        list($ip, $cidr) = explode("/", $ipAndNetmask);
        $netmask = $this->CIDRtoMask($cidr);
        return [$ip, $netmask];

    }

    private function discoverMAC($iface)
    {
        if (!$iface) {
            return "";
        }
        return shell_exec(
            "ip link show $iface |grep link/ether |
            egrep -o \"[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}\" | head -n 1"
        );
    }

    private function discoverUptime()
    {
        $uptime = trim(exec("cat /proc/uptime"));
        $uptime = explode(" ", $uptime);
        $idletime = $uptime[1];
        $uptime = $uptime[0];


        $day = 86400;
        $days = floor($uptime / $day);
        $up = "$days days, ";
        $utdelta = $uptime - ($days * $day);

        $hour = 3600;
        $hours = floor($utdelta / $hour);
        $up .= "$hours hours, ";
        $utdelta -= $hours * $hour;

        $minute = 60;
        $minutes = floor($utdelta / $minute);
        $up .= "$minutes minutes ";
        $utdelta -= round($minutes * $minute, 2);

        //echo "$utdelta seconds<br/>";
        $this->uptime = $up;
    }

    /* TODO: Check where this code came from */
    private function discoverCPU()
    {
        if ($bufr = implode(file('/proc/cpuinfo'))) {
            $processors = preg_split('/\s?\n\s?\n/', trim($bufr));
            foreach ($processors as $processor) {
                $dev = new \Grase\SystemInformation\CpuDevice();
                $details = preg_split("/\n/", $processor, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($details as $detail) {
                    $arrBuff = preg_split('/\s+:\s+/', trim($detail));
                    if (count($arrBuff) == 2) {
                        switch (strtolower($arrBuff[0])) {
                            case 'processor':
                                //$dev->load = $this->_parseProcStat('cpu'.trim($arrBuff[1]));
                                break;
                            case 'model name':
                            case 'cpu':
                                $dev->model = $arrBuff[1];
                                break;
                            case 'cpu mhz':
                            case 'clock':
                                $dev->speed = $arrBuff[1];
                                break;
                            case 'cycle frequency [hz]':
                                $dev->speed = $arrBuff[1] / 1000000;
                                break;
                            case 'cpu0clktck':
                                $dev->speed = hexdec($arrBuff[1]) / 1000000; // Linux sparc64
                                break;
                            case 'l2 cache':
                            case 'cache size':
                                $dev->cache = preg_replace("/[a-zA-Z ]/", "", $arrBuff[1]) * 1024;
                                break;
                            case 'bogomips':
                            case 'cpu0bogo':
                                $dev->bogomips = $arrBuff[1];
                                break;
                        }
                    }
                }
            }
            $this->cpu = $dev;
        }
    }

    /**
     * fill the load for a individual cpu, through parsing /proc/stat for the specified cpu
     *
     * @param String $cpuline cpu for which load should be meassured
     *
     * @return Integer
     */
    private function parseProcStat($cpuline)
    {
        $load = 0;
        $load2 = 0;
        $total = 0;
        $total2 = 0;
        if ($buf = implode(file('/proc/stat'))) {
            $lines = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^' . $cpuline . ' (.*)/', $line, $matches)) {
                    $ab = 0;
                    $ac = 0;
                    $ad = 0;
                    $ae = 0;
                    sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
                    $load = $ab + $ac + $ad; // cpu.user + cpu.sys
                    $total = $ab + $ac + $ad + $ae; // cpu.total
                    break;
                }
            }
        }
        // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
        sleep(1);
        if ($buf = implode(file('/proc/stat'))) {
            $lines = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^' . $cpuline . ' (.*)/', $line, $matches)) {
                    $ab = 0;
                    $ac = 0;
                    $ad = 0;
                    $ae = 0;
                    sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
                    $load2 = $ab + $ac + $ad;
                    $total2 = $ab + $ac + $ad + $ae;
                    break;
                }
            }
        }
        if ($total > 0 && $total2 > 0 && $load > 0 && $load2 > 0 && $total2 != $total && $load2 != $load) {
            return (100 * ($load2 - $load)) / ($total2 - $total);
        }
        return 0;
    }
}
