{include file="header.tpl" Name="List Users" activepage="users" helptext="Click on a username to edit that user.<br/>Click on the &nbsp;<u>*</u>&nbsp; to see the users password<br/>Click on ether the Data Usage or Time Usage to see the users sessions"}

<div id='userslist' >
	<table id="userslistTable" class="tablesorter">
	    <col style="width: 12em"/>
	    <col style="width: 6em"/>
	    <col span="5" style="width: 6em"/>	    	    
	    <col span="2" style="width: 7em"/>
	    <col />	
		<thead>
		<tr id='userattributesRow'>
			<th>{t}Username{/t}</th>
			<th>{t}Group{/t}</th>
			<th>{t}Data Limit{/t}</th>
			<th>{t}Data Usage (M){/t}<a class="helpbutton" title='{t}Total Data usage for the current month{/t}'><img src="/grase/images/icons/help.png" alt=""/></a></th>
			<th>{t}Data Usage (T){/t}<a class="helpbutton" title='{t}Total Data usage, from previous months, excluding current month{/t}' ><img src="/grase/images/icons/help.png" alt=""/></a></th>
			<th>{t}Time Limit{/t}</th>
			<th>{t}Time Usage (Month){/t}</th>			
			<th>{t}Account Expiry{/t}</th>
			<th>{t}Last Logoff{/t}<a class="helpbutton" title='{t}Last Logoff timestamp from current month only{/t}' ><img src="/grase/images/icons/help.png" alt=""/></a></th>
			<th>{t}Comment{/t}</th>
		</tr>
		</thead>
		<tbody>	
		{foreach from=$users item=user name=usersloop}

		<tr id="user_{$user.Username}_Row" class="userrow {$user.account_status}">
		<!-- id="user_{$user.Username}_Row" class="userrow {if $smarty.foreach.usersloop.iteration is even}even{else}odd{/if} {$user.account_status}" > -->
			<td class='info_username'><span class='info_password'>{if $user.Group eq 'Machine'}<span title="{t}Password Hidden{/t}">*</span>{else}<span title="{$user.Password}"><a href='javascript:alert("Password for {$user.Username} is {$user.Password}")'>*</a></span>{/if}</span><a href="edituser?username={$user.Username}">{$user.Username}</a></td>

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
