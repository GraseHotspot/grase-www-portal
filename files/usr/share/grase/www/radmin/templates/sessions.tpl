{include file="header.tpl" Name="Sessions" activepage="sessions"}

{if $username}<h2>{t 1=$username}Sessions for %1{/t}</h2>{/if}

<div id='sessionslist' style='display:block;'>
	<table border="0" id='sessionslistTable' class="stripeMe">
	    <col style="width: 2em"/>
	    <col span="2" style="width: 6em"/>	    	    
	    <col span="2" style="width: 4em"/>
	    <col span="1" style="width: 5em"/>	
	    <col span="3" style="width: 4em"/>		    
		<thead>
		<tr id='sessionsattributesRow' class="sessionheader">
			<th>#</th>
			<th><!--AcctStartTime-->{t}Start Time{/t}</th>
			<th><!--AcctStopTime-->{t}Stop Time{/t}</th>
			<th><!--AcctSessionTime-->{t}Time{/t}</th>
			<th><!--FramedIPAddress-->{t}IP Address{/t}</th>
			<th>{t}Username{/t}</th>
			<th>{t}Download{/t}</th>
			<th>{t}Uploaded{/t}</th>
			<th>{t}Data Usage{/t}</th>
		</tr>	
		</thead>
		<tbody>
		{foreach from=$sessions item=session name=sessionsloop}
		<tr id='session_{$session.RadAcctId}_Row' class="sessionrow {if $smarty.foreach.sessionsloop.iteration is even}even{else}odd{/if}">
			<td><span><a href="logview?acctid={$session.RadAcctId|urlencode}&amp;starttime={$session.AcctStartTime|urlencode}&amp;finishtime={$session.AcctStopTime|urlencode}&amp;ipaddress={$session.FramedIPAddress|urlencode}&amp;username={$session.Username|urlencode}">{$session.RadAcctId}</a></span></td>
			<td>{$session.AcctStartTime}</td>
			<td><a class="helpbutton" title='{t}Reason: {/t}{$session.AcctTerminateCause}'>{$session.AcctStopTime}</a></td>			
			<td>{$session.AcctSessionTime|seconds}</td>			
			<td><a class="helpbutton" title='{t mac=$session.CallingStationId}Computers hardware (MAC) address is %1{/t}'>{$session.FramedIPAddress}</a></td>
			<td>
			{if ! $session.FramedIPAddress && $session.ServiceType}
			<a class="helpbutton" title='{t}Captive Portal Config Login{/t}'>{$session.ServiceType}</a>
			{else}
			<a href="?username={$session.Username}">{$session.Username}</a>
			{/if}
			</td>
			<td>{$session.AcctInputOctets|bytes}</td>
			<td>{$session.AcctOutputOctets|bytes}</td>			
			<td>{$session.AcctTotalOctets|bytes}</td>			
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>

{include file="footer.tpl"}
