{include file="header.tpl" Name="Log" activepage="sessions"}

{if $ipaddress}<h2>{t 1=$username 2=$ipaddress}Session logs for %1 on %2{/t} &nbsp;<a class="helpbutton" title='{t}Computers hardware (MAC) address is{/t}<br/>{$session.CallingStationId}'>*</a>&nbsp;({$session.AcctTotalOctets|bytes})</h2>
<h3>{t 1=$session.AcctStartTime 2=$session.AcctStopTime}Between %1 and %2{/t}</h3>{/if}

{t}Total HTTP (WWW) Traffic Size:{/t} {$http_traffic_size}<br/>
<div id='logtables'>
<div id='domain' style='display:block;'>
	<table border="0" id='domainTable'>
		<tr class='domainattributesRow'>
			<th>{t}Domain{/t}</th>
			<th>{t}Count{/t}</th>
			<th>{t}Size{/t}</th>
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
			<th>Domain</th>
			<th>Count</th>
			<th>Size</th>
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
			<td>{t}Timestamp{/t}</td>
			<td>{t}Address{/t}</td>
<!--			<td>{t}Host{/t}</td>-->
			<td>{t}Cached{/t}</td>
			<td>{t}Request Type{/t}</td>
			<td>{t}Size{/t}</td>
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
