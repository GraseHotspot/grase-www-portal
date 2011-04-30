{include file="header.tpl" Name="Sessions" activepage="sessions"}

{if $username}<h2>{t 1=$username}Sessions for %1{/t}</h2>{/if}

<div id='sessionslist' style='display:block;'>
	<table border="0" id='sessionslistTable'>
		<thead>
		<tr id='sessionsattributesRow' class="sessionheader">
			<td>#</td>
			<td><!--AcctStartTime-->{t}Start Time{/t}</td>
			<td><!--AcctStopTime-->{t}Stop Time{/t}</td>
			<td><!--AcctSessionTime-->{t}Time{/t}</td>
			<td><!--FramedIPAddress-->{t}IP Address{/t}</td>
			<td>{t}Username{/t}</td>
			<td>{t}Download{/t}</td>
			<td>{t}Uploaded{/t}</td>
			<td>{t}Data Usage{/t}</td>
		</tr>	
		</thead>
		<tbody>
		{foreach from=$sessions item=session name=sessionsloop}
		<tr id='session_{$session.RadAcctId}_Row' class="sessionrow {if $smarty.foreach.sessionsloop.iteration is even}even{else}odd{/if}">
			<td><span><a href="logview?acctid={$session.RadAcctId|urlencode}&amp;starttime={$session.AcctStartTime|urlencode}&amp;finishtime={$session.AcctStopTime|urlencode}&amp;ipaddress={$session.FramedIPAddress|urlencode}&amp;username={$session.Username|urlencode}">{$session.RadAcctId}</a></span></td>
			<td>{$session.AcctStartTime}</td>
			<td>{$session.AcctStopTime}</td>			
			<td>{$session.AcctSessionTime|seconds}</td>			
			<td><a class="helpbutton" title='{t mac=$session.CallingStationId}Computers hardware (MAC) address is %1{/t}'>{$session.FramedIPAddress}</a></td>
			<td><a href="?username={$session.Username}">{$session.Username}</a></td>
			<td>{$session.AcctInputOctets|bytes}</td>
			<td>{$session.AcctOutputOctets|bytes}</td>			
			<td>{$session.AcctTotalOctets|bytes}</td>			
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>

{include file="footer.tpl"}
