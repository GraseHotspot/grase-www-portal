{include file="header.tpl" Name="Sessions" activepage="sessions"}

{if $username}<h2>{t 1=$username}Sessions for %1{/t}</h2>{/if}

{if !$activesessions && !$sessions}
<h2>{t}Active Sessions{/t}</h2>
<h3>{t}No active sessions{/t}</h3>
<a href="?allsessions">{t}Display all Sessions{/t}</a>
{/if}

{if $activesessions}
<h2>{t}Active Sessions{/t}</h2>
<a href="?allsessions">{t}Display all Sessions{/t}</a> | {if $autorefresh}<a href="?">{t}Stop Auto Refresh Page{/t}</a> (Last refresh {$smarty.now|date_format:"%Y-%m-%d %T"}){else}<a href="?refresh=5">{t}Auto Refresh Page{/t}</a>{/if}

<div id='activesessionslist' style='display:block;'>
	<table border="0" id='activesessionslistTable' class="stripeMe">
	    <col style="width: 2em"/>
	    <col span="2" style="width: 6em"/>	    	    
	    <col span="1" style="width: 3em"/>
	    <col span="3" style="width: 5em"/>
        <col span="1" style="width: 3em"/>
		<thead>
		<tr id='activesessionsattributesRow' class="sessionheader">
			<th>#</th>
			<th><!--AcctStartTime-->{t}Start Time{/t}</th>
			<th><!--AcctStopTime-->{t}Stop Time{/t}</th>
			<th><!--AcctSessionTime-->{t}Time{/t}</th>
			<th><!--FramedIPAddress-->{t}IP/MAC Address{/t}</th>
			<th>{t}Username{/t}</th>
			<th>{t}Data Usage{/t}</th>
            <th>{t}Logout Session{/t}</th>
		</tr>	
		</thead>
		<tbody>
		{foreach from=$activesessions item=session name=sessionsloop}
		<tr id='session_{$session.RadAcctId}_Row' class="sessionrow {if $smarty.foreach.sessionsloop.iteration is even}even{else}odd{/if}">
			<td><span><a href="logview?acctid={$session.RadAcctId|urlencode}&amp;starttime={$session.AcctStartTime|urlencode}&amp;finishtime={$session.AcctStopTime|urlencode}&amp;ipaddress={$session.FramedIPAddress|urlencode}&amp;username={$session.Username|urlencode}">{$session.RadAcctId}</a></span></td>
			<td>{$session.AcctStartTime}</td>
			<td><a class="helpbutton" title='{t}Reason: {/t}{$session.AcctTerminateCause}'>{$session.AcctStopTime}</a></td>			
			<td title='{$session.AcctSessionTime}'>{$session.AcctSessionTime|seconds}</td>			
			<td>{$session.FramedIPAddress} {if $session.CallingStationId}<br/>{$session.CallingStationId}{/if}</td>
			<td>
			{if ! $session.FramedIPAddress && $session.ServiceType}
			<a class="helpbutton" title='{t}Captive Portal Config Login{/t}'>{$session.ServiceType}</a>
			{else}
			<a href="?username={$session.Username}">{$session.Username}</a><a class="ui-icon ui-icon-person" style="display:inline-block" href="edituser?username={$session.Username}"></a>
			{/if}
			</td>
			<td title='{$session.AcctTotalOctets}'>{$session.AcctTotalOctets|bytes}<br/><span class="ui-icon ui-icon-arrowthick-1-s" style="display:inline-block"></span>{$session.AcctInputOctets|bytes} <span class="ui-icon ui-icon-arrowthick-1-n" style="display:inline-block"></span>{$session.AcctOutputOctets|bytes} </td>
            <td>{if ! $session.AcctStopTime && $session.CallingStationId != "00-00-00-00-00-00"}
                    <form method="post">
                        <button class="negative btn btn-danger" type="submit" name="logout_mac" value="{$session.CallingStationId}" onClick="return confirm('{t}Are you sure you want to logout this session?{/t}')"><i class="fa fa-times"></i></button>
                    </form>
                {/if}
            </td>
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>
{/if}

{if $sessions}
<h2>{t}All Sessions{/t}</h2>
<a href="?">{t}Display just active Sessions{/t}</a>

{include file="paginate.tpl"}
<div id='sessionslist' style='display:block;'>
	<table border="0" id='sessionslistTable' class="stripeMe">
	    <col style="width: 2em"/>
	    <col span="2" style="width: 6em"/>
	    <col span="1" style="width: 3em"/>
	    <col span="3" style="width: 5em"/>
        <col span="1" style="width: 3em"/>
		<thead>
		<tr id='sessionsattributesRow' class="sessionheader">
			<th>#</th>
			<th><!--AcctStartTime-->{t}Start Time{/t}</th>
			<th><!--AcctStopTime-->{t}Stop Time{/t}</th>
			<th><!--AcctSessionTime-->{t}Time{/t}</th>
			<th><!--FramedIPAddress-->{t}IP/MAC Address{/t}</th>
			<th>{t}Username{/t}</th>
			<th>{t}Data Usage{/t}</th>
            <th>{t}Logout Session{/t}</th>
		</tr>	
		</thead>
		<tbody>
		{foreach from=$sessions item=session name=sessionsloop}
		<tr id='session_{$session.RadAcctId}_Row' class="sessionrow {if $smarty.foreach.sessionsloop.iteration is even}even{else}odd{/if}">
			<td><span><a href="logview?acctid={$session.RadAcctId|urlencode}&amp;starttime={$session.AcctStartTime|urlencode}&amp;finishtime={$session.AcctStopTime|urlencode}&amp;ipaddress={$session.FramedIPAddress|urlencode}&amp;username={$session.Username|urlencode}">{$session.RadAcctId}</a></span></td>
			<td>{$session.AcctStartTime}</td>
			<td><a class="helpbutton" title='{t}Reason: {/t}{$session.AcctTerminateCause}'>{$session.AcctStopTime}</a></td>			
			<td title='{$session.AcctSessionTime}'>{$session.AcctSessionTime|seconds}</td>			
			<td>{$session.FramedIPAddress} {if $session.CallingStationId}<br/>{$session.CallingStationId}{/if}</td>
			<td>
			{if ! $session.FramedIPAddress && $session.ServiceType}
			<a class="helpbutton" title='{t}Captive Portal Config Login{/t}'>{$session.ServiceType}</a>
			{else}
			<a href="?username={$session.Username}">{$session.Username}</a><a class="ui-icon ui-icon-person" style="display:inline-block" href="edituser?username={$session.Username}"></a>
			{/if}
			</td>
			<td title='{$session.AcctTotalOctets}'>{$session.AcctTotalOctets|bytes}<br/><span class="ui-icon ui-icon-arrowthick-1-s" style="display:inline-block"></span>{$session.AcctInputOctets|bytes} <span class="ui-icon ui-icon-arrowthick-1-n" style="display:inline-block"></span>{$session.AcctOutputOctets|bytes} </td>
            <td>{if ! $session.AcctStopTime && $session.CallingStationId != "00-00-00-00-00-00"}
                    <form method="post">
                        <button class="negative btn btn-danger" type="submit" name="logout_mac" value="{$session.CallingStationId}" onClick="return confirm('{t}Are you sure you want to logout this session?{/t}')"><i class="fa fa-times"></i></button>
                    </form>
                {/if}
            </td>

        </tr>
		{/foreach}
		</tbody>
	</table>
</div>
{/if}

{include file="footer.tpl"}
