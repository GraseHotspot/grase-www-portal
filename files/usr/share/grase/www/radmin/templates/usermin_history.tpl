{include file="header.tpl" Name="Sessions" activepage="history"}

<h2>My History</h2>

<div id='sessionslist' style='display:block;'>
	<table border="0" id='sessionslistTable'>
		<thead>
		<tr id='sessionsattributesRow' class="sessionheader">
			<td>#</td>
			<td><!--AcctStartTime-->Start Time</td>
			<td><!--AcctStopTime-->Stop Time</td>
			<td><!--AcctSessionTime-->Time</td>
			<td><!--FramedIPAddress-->IP Address</td>
			<td>Download</td>
			<td>Uploaded</td>
			<td>Data Usage</td>
		</tr>
		</thead>
		<tbody>
		{foreach from=$sessions item=session name=sessionsloop}
		<tr id='session_{$session.RadAcctId}_Row' class="sessionrow {if $smarty.foreach.sessionsloop.iteration is even}even{else}odd{/if}">
			<td><span>{$session.RadAcctId}</span></td>
			<td>{$session.AcctStartTime}</td>
			<td>{$session.AcctStopTime}</td>
			<td>{$session.AcctSessionTime|seconds}</td>
			<td><a class="helpbutton" title='Computers hardware (MAC) address is<br/>{$session.CallingStationId}'>{$session.FramedIPAddress}</a></td>
			<td>{$session.AcctInputOctets|bytes}</td>
			<td>{$session.AcctOutputOctets|bytes}</td>
			<td>{$session.AcctTotalOctets|bytes}</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>

{include file="footer.tpl"}
