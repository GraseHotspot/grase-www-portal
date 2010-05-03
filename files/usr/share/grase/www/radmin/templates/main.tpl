{include file="header.tpl" Name="Main" activepage="main"}

<h3>User Management Interface</h3>

<table>
<tbody><tr><td colspan="3" class="headline"><nobr>Status</nobr></td></tr>


<tr><td class="header" colspan="3"><nobr>Device Information</nobr></td></tr>
<tr><td class="title"><nobr>Model Name</nobr></td><td colspan="2"><nobr>{$Application}</nobr></td></tr>

<tr><td class="title" width="35%"><nobr><a href="/configuration/hostname.html">Host Name </a></nobr><a href="/configuration/hostname.html"><img src="/images/link1.gif" align="middle" border="0" hspace="0" vspace="0"></a></td><td colspan="2">{$Sysinfo->hostname}</td></tr>

<tr><td class="title"><nobr>System Up-Time</nobr></td><td colspan="2">{$Sysinfo->uptime}</td></tr>

<tr><td class="title"><a href="/configuration/sntp_client.html"><nobr>Current Time </nobr><img src="/images/link1.gif" align="middle" border="0" hspace="0" vspace="0"></a></td><td>{php}echo date('r'){/php}</td><td><!--<input onclick="location='/configuration/sntp_client_sync.html'" value="Sync Now" type="button">--></td></tr>

<tr><td class="title"><nobr>Hardware Version</nobr></td><td colspan="2">{$Sysinfo->cpu->model} @{$Sysinfo->cpu->speed}MHz</td></tr>

<tr><td class="title"><nobr>Software Version</nobr></td><td colspan="2">{$application_version}</td></tr>

<tr><td class="title"><nobr>Home URL</nobr></td><td colspan="2"><a href="http://hotspot.purewhite.id.au">GRASE (Purewhite)</a></td></tr>


<tr><td class="header" colspan="3"><nobr>LAN</nobr></td></tr>
<tr><td class="title"><nobr>IP Address</nobr></td><td colspan="2"><nobr>{$Sysinfo->lan->ipaddress}</nobr></td></tr>
<tr><td class="title"><nobr>Subnet Mask</nobr></td><td colspan="2"><nobr>{$Sysinfo->lan->netmask}</nobr></td></tr>
<tr><td class="title"><nobr>MAC Address</nobr></td><td colspan="2"><nobr>{$Sysinfo->lan->mac|upper}</nobr></td></tr>
<tr><td class="title"><nobr>Interface</nobr></td><td colspan="2"><nobr>{$Sysinfo->lan->iface}</nobr></td></tr>

<tr><td class="header" colspan="3"><nobr>WAN</nobr></td></tr>
<tr><td class="title"><nobr>IP Address</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->ipaddress}</nobr></td></tr>
<tr><td class="title"><nobr>Subnet Mask</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->netmask}</nobr></td></tr>
<tr><td class="title"><nobr>Gateway</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->gateway}</nobr></td></tr>
<tr><td class="title"><nobr>DNS 1</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->dns_primary}</nobr></td></tr>
<tr><td class="title"><nobr>DNS 2</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->dns_secondary}</nobr></td></tr>
<tr><td class="title"><nobr>MAC Address</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->mac|upper}</nobr></td></tr>
<tr><td class="title"><nobr>Interface</nobr></td><td colspan="2"><nobr>{$Sysinfo->wan->iface}</nobr></td></tr>

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
