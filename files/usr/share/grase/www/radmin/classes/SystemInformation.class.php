<?php

class NetworkInterface
{
    public $ipaddress;
    public $iface;
    public $gateway;
    public $mac;
    public $netmask;
    public $dns_primary;
    public $dns_secondary;
    
/*    public __construct()
    {
    
    }*/
}

class CpuDevice
{
	public $load;
	public $speed;
	public $model;
	public $cache;
	public $bogomips;
}

class SystemInformation
{
    public $lan;
    public $wan;
    public $cpu;
    public $hostname;
    public $uptime;
    
    public function __construct()
    {
        $this->lan = new NetworkInterface();
        $this->wan = new NetworkInterface();
        $this->cpu = new CpuDevice(); // Only takes information for 1 core/processor
        
        // Load Settings in correct order
        $this->LAN_Interface();
        $this->WAN_Interface();
        $this->Hostname();
        $this->Uptime();
        $this->cpu();
    }

    public function Hostname()
    {
        $this->hostname = gethostbyaddr (gethostbyname ($_SERVER["SERVER_NAME"])); 
    }
    
    public function SoftwareVersion()
    {
    
    }
    
    private function LAN_Interface()
    {
        // Destination     Gateway         Genmask         Flags Metric Ref    Use Iface
        $details = preg_split ("/\s+/", `/sbin/route -n |grep -v 10.64.63|grep tun`);
        $this->lan->netmask = $details[2];
        $this->lan->iface = $details[7];
        
        if($this->lan->iface)
        {        

            $this->lan->mac = $this->MAC($this->lan->iface);        
            $this->lan->ipaddress = $this->IP_Address($this->lan->iface);        
            $this->lan->netmask = $this->Netmask($this->lan->iface);
        }
        
    }
    
    private function WAN_Interface()
    {
        // Destination     Gateway         Genmask         Flags Metric Ref    Use Iface
        $lines = preg_split ("/\n/", trim(`/sbin/route -n |grep eth`));
        $details = preg_split ("/\s+/",$lines[0]);
        $this->wan->netmask = $details[2];
        $this->wan->iface = $details[7];
        $details = preg_split ("/\s+/",end($lines));        
        $this->wan->gateway = $details[1];
        
        $this->wan->mac = $this->MAC($this->wan->iface);
        $this->wan->ipaddress = $this->IP_Address($this->wan->iface);
        $this->DNS($this->wan);
        
    }
   
    public function DNS($ifaceobj)
    {
        $nameservers = preg_split("/\n/", trim(shell_exec("grep nameserver /etc/resolv.conf")));
        $ifaceobj->dns_primary = end(preg_split("/\s+/", $nameservers[0],2));
        $ifaceobj->dns_secondary = end(preg_split("/\s+/", $nameservers[1],2));        

    }
    
    private function IP_Address($iface)
    {
        return shell_exec("/sbin/ifconfig $iface|egrep -i -o 'inet addr:[0-9.]+'| egrep -o \"[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\"|head -n 1");
        
    }
    
    private function Netmask($iface)
    {
        return shell_exec("/sbin/ifconfig $iface|egrep -i -o  mask\:[0-9.]+| egrep -o \"[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\"|head -n 1");        
    }    
    
    private function MAC($iface)
    {
        return shell_exec("/sbin/ifconfig $iface|grep -i Hwaddr| egrep -o \"[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}:[0-9a-g]{2}\"|head -n 1");
    }
    
    private function Uptime()
    {
        $uptime = trim(exec("cat /proc/uptime"));
        $uptime = explode(" ", $uptime);
        $idletime=$uptime[1];
        $uptime=$uptime[0];


        $day=86400;
        $days=floor($uptime/$day);
        $up = "$days days, ";
        $utdelta=$uptime-($days*$day);

        $hour=3600;
        $hours=floor($utdelta/$hour);
        $up .= "$hours hours, ";
        $utdelta-=$hours*$hour;

        $minute=60;
        $minutes=floor($utdelta/$minute);
        $up .= "$minutes minutes ";
        $utdelta-=round($minutes*$minute,2);

        //echo "$utdelta seconds<br/>";
        $this->uptime = $up;
    }

    private function cpu(){
		if ($bufr = implode(file('/proc/cpuinfo'))) {
		    $processors = preg_split('/\s?\n\s?\n/', trim($bufr));
		    foreach ($processors as $processor) {
		        $dev = new CpuDevice();
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
		                    $dev->model =$arrBuff[1];
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
		                    $dev->cache = preg_replace("/[a-zA-Z]/", "", $arrBuff[1]) * 1024;
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
    private function _parseProcStat($cpuline)
    {
        $load = 0;
        $load2 = 0;
        $total = 0;
        $total2 = 0;
        if ($buf = implode(file('/proc/stat'))) {
            $lines = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^'.$cpuline.' (.*)/', $line, $matches)) {
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
                if (preg_match('/^'.$cpuline.' (.*)/', $line, $matches)) {
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

?>
