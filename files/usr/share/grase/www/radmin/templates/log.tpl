{include file="header.tpl" Name="Log" activepage="sessions"}

{if $ipaddress}<h2>Session logs for {$username} on&nbsp;<a class="helpbutton" title='Computers hardware (MAC) address is<br/>{$session.CallingStationId}'>{$ipaddress}</a>&nbsp;({$session.AcctTotalOctets|bytes})</h2>
<h3>Between {$session.AcctStartTime} and {$session.AcctStopTime}</h3>{/if}

Total HTTP (WWW) Traffic Size: {$http_traffic_size}<br/>
<div id='logtables'>
<div id='domain' style='display:block;'>
	<table border="0" id='domainTable'>
		<tr class='domainattributesRow'>
			<td>Domain</td>
			<td>Count</td>
			<td>Size</td>
		</tr>	

		{counter assign=idx print=0 name=domaintally}
		{foreach from=$domain_tally item=domain key=domainname}
		{counter name=domaintally}
		{if $idx <= 10}
			<tr>
				<td>{$domainname}</td>
				<td>{$domain}</td>
				<td>{$domain_formatsize[$domainname]}</td>
			</tr>
		{/if}

		{/foreach}
	</table>
</div>

<div id='domainsize' style='display:block;'>
	<table border="0" id='domainsizeTable'>
		<tr class='domainattributesRow'>
			<td>Domain</td>
			<td>Count</td>
			<td>Size</td>
		</tr>	

		{counter assign=idy print=0 name=domainsizes}
		{foreach from=$domain_size item=domain key=domainname}
		{counter name=domainsizes}
		{if $idy <= 10}
			<tr>
				<td>{$domainname}</td>
				<td>{$domain_tally[$domainname]}</td>
				<td>{$domain_formatsize[$domainname]}</td>
			</tr>
		{/if}

		{/foreach}
	</table>
</div>

<div id='log' style='display:block;'>
	<table border="0" id='logTable'>
		<tr class='logattributesRow'>
			<td>Timestamp</td>
			<td>Address</td>
<!--			<td>Host</td>-->
			<td>Cached</td>
			<td>Request Type</td>
			<td>Size</td>
		</tr>	
		{foreach from=$loglines item=logline}
		<tr>
			<td>{$logline.timestamp}</td>
			<td><a href="{$logline.URL}">{$logline.URL|truncate:30}</a></td>
<!--			<td>{$logline.host}</td>-->
			<td>{$logline.cached}</td>
			<td>{$logline.request}</td>
			<td>{$logline.size}</td>															
		</tr>
		{/foreach}
	</table>
</div>

</div>

{include file="footer.tpl"}
