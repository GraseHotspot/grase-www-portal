{include file="header.tpl" Name="Display Users" activepage="users" helptext="Click on a username to edit that user.<br/>Click on the &nbsp;<u>*</u>&nbsp; to see the users password<br/>Click on ether the Data Usage or Time Usage to see the users sessions"}

<div class="errorPage" style="display: {if $error}block;{else}none;{/if}"> <span id="errorMessage">{$error}</span> </div>
<div id='userslist' >
	<table border="0" id='userslistTable'>
		<thead>
		<tr id='userattributesRow'>
			<td>Username</td>
<!--			<td>Group</td>-->
			<td>Data Limit</td>
			<td>Data Usage (M)<a class="helpbutton" onclick="ShowContent('helpbox','Total Data usage for the current month');" ><img src="/images/icons/help.png" alt=""/></a></td>
			<td>Data Usage (T)<a class="helpbutton" onclick="ShowContent('helpbox','Total Data usage, from previous months, excluding current month');" ><img src="/images/icons/help.png" alt=""/></a></td>
			<td>Time Limit</td>
			<td>Time Usage(Month)</td>			
			<td>Account Expiry</td>
			<td>Last Logoff<a class="helpbutton" onclick="ShowContent('helpbox','Last Logoff timestamp from current month only');" ><img src="/images/icons/help.png" alt=""/></a></td>
			<td>Comment</td>
		</tr>
		</thead>
		{foreach from=$users_groups item=group name=grouploop key=id}
		<tr><td id='{$id}_header' colspan='9' class='groupheader' onclick='switchMenu("{$id}")'>{$id}</td></tr>
		<tbody id='{$id}_body'>	

		{foreach from=$group|@sortby:"-Group,-#AcctTotalOctets,Username" item=user key=id name=usersloop}
		<tr id="user_{$user.Username}_Row" class="userrow {if $smarty.foreach.usersloop.iteration is even}even{else}odd{/if} {$user.account_status}" >
			<td class='info_username'><span class='info_password'>{if $user.Group eq 'Machine'}<span title="Password Hidden">*</span>{else}<span title="{$user.Password}"><a href='javascript:alert("Password for {$user.Username} is {$user.Password}")'>*</a></span>{/if}</span><a href="edituser?username={$user.Username}">{$user.Username}</a></td>

<!--			<td class='info_group'>{$user.Group}</td>-->
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
		{/foreach}

	</table>
</div>

{include file="footer.tpl"}
