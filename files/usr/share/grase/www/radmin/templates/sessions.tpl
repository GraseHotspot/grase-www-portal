{include file="header.tpl" Name="Sessions" activepage="sessions"}

{if $username}<h2>Sessions for {$username}</h2>{/if}

<div id='sessionslist' style='display:block;'>
	<table border="0" id='sessionslistTable'>
		<thead>
		<tr id='sessionsattributesRow' class="sessionheader">
			<td>#</td>
			<td><!--AcctStartTime-->Start Time</td>
			<td><!--AcctStopTime-->Stop Time</td>
			<td><!--AcctSessionTime-->Time</td>
			<td><!--FramedIPAddress-->IP Address</td>
			<td>Username</td>
			<td>Download</td>
			<td>Uploaded</td>
			<td>Data Usage</td>
		</tr>	
		</thead>
		<tbody>
		{foreach from=$sessions item=session name=sessionsloop}
		<tr id='session_{$session.RadAcctId}_Row' class="sessionrow {if $smarty.foreach.sessionsloop.iteration is even}even{else}odd{/if}">
			<td><span><a href="logview?acctid={$session.RadAcctId|urlencode}&amp;starttime={$session.AcctStartTime|urlencode}&amp;finishtime={$session.AcctStopTime|urlencode}&amp;ipaddress={$session.FramedIPAddress|urlencode}&amp;username={$session.Username|urlencode}">{$session.RadAcctId}</a></span></td>
			<td>{$session.AcctStartTime}</td>
			<td>{$session.AcctStopTime}</td>			
			<td>{$session.AcctSessionTime|seconds}</td>			
			<td><a class="helpbutton" title='Computers hardware (MAC) address is<br/>{$session.CallingStationId}'>{$session.FramedIPAddress}</a></td>
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
