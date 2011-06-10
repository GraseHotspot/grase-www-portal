{include file="header.tpl" Name="List Users" activepage="users" helptext="Click on a username to edit that user.
Click on the * to see the users password
Click on ether the Data Usage or Time Usage to see the users sessions"}

<div id='userslist' style="overflow:hidden;"><!-- jQuery UI bug #5601 requires overflow:hidden style due to float left menu -->
    <ul id="tabselector">
    {foreach from=$users_groups item=group key=groupname name=groupmenuloop}
        <li><a href="#list{$groupname|underscorespaces}">{$groupname}</a></li>
    {/foreach}
    </ul>
    {foreach from=$users_groups item=group key=groupname name=grouploop}
    <div id="list{$groupname|underscorespaces}" class="tabcontent">
	<table id="{$groupname}userslistTable" class="userslistTable stripeMe">
	    <col style="width: 6em"/>
	    {if $groupname == 'All'}<col style="width: 5em"/>{/if}
	    <col span="4" style="width: 6em"/>	    	    
	    <col span="3" style="width: 7em"/>
	    <col span="1" style="width: 6em"/>	
		<thead>
		<tr id='{$groupname}userattributesRow' class="userattributesRow">
			<th>{t}Username{/t}</th>
		    {if $groupname == 'All'}<th>{t}Group{/t}</th>{/if}
			<th>{t}Data Limit{/t}</th>
			<th><a class="helpbutton ui-icon ui-icon-info" title='{t}Total Data usage for the current month{/t}'><img src="/grase/images/icons/help.png" alt=""/></a> {t}Data Usage (M){/t}</th>
			<th><a class="helpbutton ui-icon ui-icon-info" title='{t}Total Data usage, from previous months, excluding current month{/t}' ><img src="/grase/images/icons/help.png" alt=""/></a> {t}Data Usage (T){/t}</th>
			<th>{t}Time Limit{/t}</th>
			<th>{t}Time Usage (Month){/t}</th>			
			<th>{t}Account Expiry{/t}</th>
			<th><a class="helpbutton ui-icon ui-icon-info" title='{t}Last Logoff timestamp from current month only{/t}' ><img src="/grase/images/icons/help.png" alt=""/></a> {t}Last Logoff{/t}</th>
			<th>{t}Comment{/t}</th>
		</tr>
		</thead>
		<tbody>	
		{foreach from=$group item=user name=usersloop}

		<tr id="user_{$user.Username}_{$groupname}_Row" class="userrow {$user.account_status}">
			<td class='info_username'><span class='info_password'>{if $user.Group eq 'Machine'}<span title="{t}Password Hidden{/t}">*</span>{else}<span title="{$user.Password}"><a href='javascript:alert("Password for {$user.Username} is {$user.Password}")'>*</a></span>{/if}</span><a href="edituser?username={$user.Username}">{$user.Username}</a></td>

			{if $groupname == 'All'}<td class='info_group'>{$user.Group}</td>{/if}
			<td class='info_datalimit' title='{$user.MaxOctets}'>{$user.MaxOctets|bytes}</td>
			<td class='info_datausage' title='{$user.AcctTotalOctets}'><a href="sessions?username={$user.Username}">{$user.AcctTotalOctets|bytes}</a></td>			
			<td class='info_datausage_t' title='{$user.TotalOctets}'>{$user.TotalOctets|bytes}</td>			
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
    {/foreach}

    	
</div>

{include file="footer.tpl"}
