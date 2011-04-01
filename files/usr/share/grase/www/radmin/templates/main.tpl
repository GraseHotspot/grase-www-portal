{include file="header.tpl" Name="Main" activepage="main"}

<h3>User Management Interface</h3>

<table>
<tbody><tr><td colspan="3" class="header">Status</td></tr>


<tr><td class="header" colspan="3">Device Information</td></tr>
<tr><td class="title">Model Name</td><td colspan="2">{$Application}</td></tr>

<tr><td class="title">Host Name</td><td colspan="2">{$Sysinfo->hostname}</td></tr>

<tr><td class="title">System Up-Time</td><td colspan="2">{$Sysinfo->uptime}</td></tr>

<tr><td class="title">Current Time </td><td>{php}echo date('r'){/php}</td><td></td></tr>

<tr><td class="title">Hardware Version</td><td colspan="2">{$Sysinfo->cpu->model} @{$Sysinfo->cpu->speed}MHz</td></tr>

<tr><td class="title">Software Version</td><td colspan="2">{$application_version}</td></tr>

<tr><td class="title">Home URL</td><td colspan="2"><a href="http://hotspot.purewhite.id.au">GRASE (Purewhite)</a></td></tr>


<tr><td class="header" colspan="3">LAN</td></tr>
<tr><td class="title">IP Address</td><td colspan="2">{$Sysinfo->lan->ipaddress}</td></tr>
<tr><td class="title">Subnet Mask</td><td colspan="2">{$Sysinfo->lan->netmask}</td></tr>
<tr><td class="title">MAC Address</td><td colspan="2">{$Sysinfo->lan->mac|upper}</td></tr>
<tr><td class="title">Interface</td><td colspan="2">{$Sysinfo->lan->iface}</td></tr>

<tr><td class="header" colspan="3">WAN</td></tr>
<tr><td class="title">IP Address</td><td colspan="2">{$Sysinfo->wan->ipaddress}</td></tr>
<tr><td class="title">Subnet Mask</td><td colspan="2">{$Sysinfo->wan->netmask}</td></tr>
<tr><td class="title">Gateway</td><td colspan="2">{$Sysinfo->wan->gateway}</td></tr>
<tr><td class="title">DNS 1</td><td colspan="2">{$Sysinfo->wan->dns_primary}</td></tr>
<tr><td class="title">DNS 2</td><td colspan="2">{$Sysinfo->wan->dns_secondary}</td></tr>
<tr><td class="title">MAC Address</td><td colspan="2">{$Sysinfo->wan->mac|upper}</td></tr>
<tr><td class="title">Interface</td><td colspan="2">{$Sysinfo->wan->iface}</td></tr>

</table>
<!--
<div id="network">
IP addresses
Gateway
</div>
<div id="coovachilli">
Current sessions
</div>
-->

{include file="footer.tpl"}
