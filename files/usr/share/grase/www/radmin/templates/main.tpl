{include file="header.tpl" Name="Status" activepage="main"}

<h3>{t}User Management Interface{/t}</h3>

<table>
<tbody><tr><td colspan="3" class="header">Status</td></tr>


<tr><td class="header" colspan="3">{t}Device Information{/t}</td></tr>
<tr><td class="title">{t}Model Name{/t}</td><td colspan="2">{$Application}</td></tr>

<tr><td class="title">{t}Host Name{/t}</td><td colspan="2">{$Sysinfo->hostname}</td></tr>

<tr><td class="title">{t}System Up-Time{/t}</td><td colspan="2">{$Sysinfo->uptime}</td></tr>

<tr><td class="title">{t}Current Server Time{/t}</td><td>{php}echo date('r'){/php}</td><td></td></tr>

<tr><td class="title">{t}Hardware Version{/t}</td><td colspan="2">{$Sysinfo->cpu->model} @{$Sysinfo->cpu->speed}MHz</td></tr>

<tr><td class="title">{t}Software Version{/t}</td><td colspan="2">{$application_version}</td></tr>

<tr><td class="title">{t}Home URL{/t}</td><td colspan="2"><a href="http://hotspot.purewhite.id.au">GRASE (Purewhite)</a></td></tr>


<tr><td class="header" colspan="3">{t}LAN{/t}</td></tr>
<tr><td class="title">{t}IP Address{/t}</td><td colspan="2">{$Sysinfo->lan->ipaddress}</td></tr>
<tr><td class="title">{t}Subnet Mask{/t}</td><td colspan="2">{$Sysinfo->lan->netmask}</td></tr>
<tr><td class="title">{t}MAC Address{/t}</td><td colspan="2">{$Sysinfo->lan->mac|upper}</td></tr>
<tr><td class="title">{t}Network Interface{/t}</td><td colspan="2">{$Sysinfo->lan->iface}</td></tr>

<tr><td class="header" colspan="3">{t}WAN{/t}</td></tr>
<tr><td class="title">{t}IP Address{/t}</td><td colspan="2">{$Sysinfo->wan->ipaddress}</td></tr>
<tr><td class="title">{t}Subnet Mask{/t}</td><td colspan="2">{$Sysinfo->wan->netmask}</td></tr>
<tr><td class="title">{t}Gateway{/t}</td><td colspan="2">{$Sysinfo->wan->gateway}</td></tr>
<tr><td class="title">{t}DNS 1{/t}</td><td colspan="2">{$Sysinfo->wan->dns_primary}</td></tr>
<tr><td class="title">{t}DNS 2{/t}</td><td colspan="2">{$Sysinfo->wan->dns_secondary}</td></tr>
<tr><td class="title">{t}MAC Address{/t}</td><td colspan="2">{$Sysinfo->wan->mac|upper}</td></tr>
<tr><td class="title">{t}Network Interface{/t}</td><td colspan="2">{$Sysinfo->wan->iface}</td></tr>
</tbody>
</table>
<p>Running services status page can be found at <a href="/grase/radmin/sysstatus">System Status</a></p>

{include file="footer.tpl"}
