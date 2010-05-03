{include file="header.tpl" Name="Create Tickets" activepage="createtickets"}

{if $valid_last_batch}<a href="printnewtickets" class="printlink">Print Last Batch of Tickets</a>{/if}
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
{if $valid_last_batch}<a href="printnewtickets" class="printlink">Print Last Batch of Tickets</a>{/if}
{/if}

<div id="createticketsForm">
<h2>Create Tickets</h2>
<div class="errorPage" style="display: {if $error}block;{else}none;{/if}"> <span id="errorMessage">{$error}</span> </div>



<form method='post' name='newtickets' action=''>


<table>

<tr><td>Number of Tickets <a class="helpbutton" onclick="ShowContent('helpbox','Maximum of 50 tickets per batch');" > <img src="/images/icons/help.png" alt=""/></a></td><td><input type="text" name="numberoftickets" value='{$user.numberoftickets}'/></td></tr>

<tr><td>Group</td><td> {html_options name="Group" options=$Usergroups selected=$user.Group}</td></tr>
<tr><td>Comment</td><td> <input type="text" name="Comment" value='{$user.Comment}'/></td></tr>


<tr><td colspan='2'>When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left)</td></tr>
<tr><td>Data Limit (Mb)</td><td>{html_options name="Max_Mb" options=$Datacosts selected=$user.Max_Mb} <span class="form_or">OR</span> <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb}' title="Type your own Mb Limit"/>
</td></tr>
<tr><td>Time Limit (Minutes)</td><td>{html_options name="Max_Time" options=$Timecosts selected=$user.Max_Time} <span class="form_or">OR</span> <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="Type your own Time Limit"/></td></tr>


<tr><td></td><td class="buttons"><button class="positive" type="submit" name="createticketssubmit" value="Create Tickets"><img src="/images/icons/tick.png" alt=""/>Create Tickets</button></td></tr>
</table>
</form>
</div>




{include file="footer.tpl"}
