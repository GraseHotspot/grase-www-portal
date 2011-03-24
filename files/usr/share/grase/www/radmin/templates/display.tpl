{include file="header.tpl" Name="Display Users" activepage="users" helptext="Click on a username to edit that user.<br/>Click on the &nbsp;<u>*</u>&nbsp; to see the users password<br/>Click on ether the Data Usage or Time Usage to see the users sessions"}

<div class="errorPage" style="display: {if $error}block;{else}none;{/if}"><span id="errorMessage">{foreach from=$error item=msg}{$msg}<br/>{/foreach}</span> </div>

<div id='userslist' >
	<table id="userslistTable" class="tablesorter">
		<thead>
		<tr id='userattributesRow'>
			<th>Username</th>
			<th>Group</th>
			<th>Data Limit</th>
			<th>Data Usage (M)<a class="helpbutton" title='Total Data usage for the current month'><img src="/grase/images/icons/help.png" alt=""/></a></th>
			<th>Data Usage (T)<a class="helpbutton" title='Total Data usage, from previous months, excluding current month' ><img src="/grase/images/icons/help.png" alt=""/></a></th>
			<th>Time Limit</th>
			<th>Time Usage(Month)</th>			
			<th>Account Expiry</th>
			<th>Last Logoff<a class="helpbutton" title='Last Logoff timestamp from current month only' ><img src="/grase/images/icons/help.png" alt=""/></a></th>
			<th>Comment</td>
		</tr>
		</thead>
		<tbody>	
		{foreach from=$users item=user name=usersloop}

		<tr>
		<!-- id="user_{$user.Username}_Row" class="userrow {if $smarty.foreach.usersloop.iteration is even}even{else}odd{/if} {$user.account_status}" > --!>
			<td class='info_username'><span class='info_password'>{if $user.Group eq 'Machine'}<span title="Password Hidden">*</span>{else}<span title="{$user.Password}"><a href='javascript:alert("Password for {$user.Username} is {$user.Password}")'>*</a></span>{/if}</span><a href="edituser?username={$user.Username}">{$user.Username}</a></td>

			<td class='info_group'>{$user.Group}</td>
			<td class='info_datalimit'>{$user.MaxOctets|bytes}</td>
			<td class='info_datausage'><a href="sessions?username={$user.Username}">{$user.AcctTotalOctets|bytes}</a></td>			
			<td class='info_datausage_t'>{$user.TotalOctets|bytes}</td>			
			<td class='info_timelimit'>{if $user.MaxAllSession>0}{$user.MaxAllSession|seconds}{/if}</td>			
			<td class='info_timeusage'><a href="sessions?username={$user.Username}">{$user.TotalTimeMonth|seconds}</a></td>						
			<td class='info_expiry'>{$user.FormatExpiration}</td>
			<td class='info_lastlogout'>{$user.LastLogout}</td>
			<td class='info_comment'>{$user.Comment}</td>			
		</tr>
		{/foreach}
		</tbody>

	</table>
    	
</div>

{include file="footer.tpl"}
