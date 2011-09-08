{include file="header.tpl" Name="Create Tickets" activepage="createtickets"}

{if $valid_last_batch}<!--<a href="printnewtickets" class="printlink" target="tickets">Print Last Batch of Tickets</a>-->{/if}
{if $createdusers}
<div id='createdtickets' class="" >
    <h2>Last Created Tickets</h2>
		<table border="0" id='userslistTable'>
			<thead>
			<tr id='userattributesRow'>
				<td>Username</td>
				<td>Password</td>
				<td>Data Limit</td>
				<td>Time Limit</td>
				<td>Account Expiry</td>
				<td>Comment</td>
			</tr>
			</thead>
			<tbody id='{$id}_body'>	

			{foreach from=$createdusers item=ticket key=id name=usersloop}
			<tr id="user_{$ticket.Username}_Row" class="userrow {if $smarty.foreach.usersloop.iteration is even}even{else}odd{/if} {$ticket.account_status}" >
				<td class='info_username'><a href="edituser?username={$ticket.Username}">{$ticket.Username}</a></td>
				<td class='info_password'>{$ticket.Password}</td>

				<td class='info_datalimit'>{$ticket.MaxOctets|bytes}</td>
				<td class='info_timelimit'>{if $ticket.MaxAllSession>0}{$ticket.MaxAllSession|seconds}{/if}</td>			
				<td class='info_expiry'>{$ticket.FormatExpiration}</td>
				<td class='info_comment'>{$ticket.Comment}</td>			
			</tr>
			{/foreach}
			</tbody>

		</table>
	</div>
{/if}
{if $valid_last_batch}<a href="printnewtickets" class="printlink" target="tickets">Print Last Batch of Tickets</a>{/if}


<div id="createticketsForm">
<h2>Create Tickets</h2>


<form method='post' id='newticketsform' action='?' class='generalForm'>

<div>
    <label for='numberoftickets'>Number of Tickets</label>
    <input type="text" name="numberoftickets" id="numberoftickets"  value='{$user.numberoftickets}' autofocus="autofocus" required="required"/>
    <span id="numberofticketsInfo">Maximum of 50 tickets per batch</span>
</div>
<div>
    <label for='Group'>Group</label>
    {html_options name="Group" id="Group" options=$Usergroups selected=$user.Group}    
    <span id='GroupInfo'>Choose the users group (Expiry is based on the user group)</span>
    <br/>{include file="grouppropertiesinfo.tpl"}
</div>
<div>
    <label for='Comment'>Comment</label>
    <input type="text" name="Comment" id="Comment" value='{$user.Comment}'/>
    <span id='CommentInfo'>A comment that is applied to all tickets</span>
</div>

    <p><span>{t}When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left){/t}<br/>
    {t}A limit of 0 does not mean unlimited, it will immediately lock the user out. To have an unlimited user, the user must be created without any limits.{/t}<br/><strong>{t}If a limit is not set here, but is defined for the group, then the group limit will apply{/t}</strong></span></p>

<div>
    <label for='MaxMb'>Data Limit (MiB)</label>
    {html_options name="Max_Mb" options=$Datacosts selected=$user.Max_Mb}
    <span class="form_or">OR</span>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb}' title="Type your own Mb Limit"/>
    <span id='Max_MbInfo'>Choose a Data Limit OR Type your own value</span>
</div>
<div>
    <label for='MaxTime'>Time Limit (Minutes)</label>
    {html_options name="Max_Time" options=$Timecosts selected=$user.Max_Time}
    <span class="form_or">OR</span>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="Type your own Time Limit"/>
    <span id='Max_TimeInfo'>Choose a Time Limit OR Type your own value</span>
</div>

       <p><button type="submit" name="createticketssubmit" value="Create Tickets"><img src="/grase/images/icons/tick.png" alt=""/>Create Tickets</button></p>

</form>
</div>




{include file="footer.tpl"}
