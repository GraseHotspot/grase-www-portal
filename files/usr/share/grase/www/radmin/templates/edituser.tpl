{include file="header.tpl" Name="Edit User" activepage="users"}

<div id="edituserForm">
<h2>Edit User</h2>

{if $user.Group eq MACHINE_GROUP_NAME}
<div class="errorPage" style="display: block;"> <span id="errorMessage">Machine Account Locked (Only comments can be changed){if $error}<br/>{foreach from=$error item=msg}{$msg}<br/>{/foreach}{/if}</span> </div>

<table>
	<tr><td>Username</td><td>{$user.Username}</td></tr>
	<!--<tr><td>Comment</td><td>{$user.Comment}</td></tr>	-->
	<tr><td>Group</td><td>{$user.Group}</td></tr>
	<tr><td>Data Limit (Mb)</td><td>{$user.MaxMb}</td></tr>
	<tr><td>Expiry</td><td>{$user.Expiration}</td></tr>
	
	<tr><td>Comment</td><td><form method='post'> <input type="text" name="Comment" value='{$user.Comment}'/><button type="submit" name="changecommentsubmit" value="Change Comment">Change Comment</button></form></td></tr>
</table>
<table>	
<tr><td colspan='2' style='color: red'>If you really want to delete this machine account, please type exactly "Y<span style="display:none;">PLEASE DON'T COPY AND PASTE</span>es, I want to delete this use<span style="display:none;">PLEASE DON'T COPY AND PASTE</span>r" in the below box then click "Delete User"</td><tr/>
	<tr><td></td><td><form method='post'><input size='30' type="text" name="DeleteUser"/><button class="negative" type="submit" name="deleteusersubmit" value="Delete User"><img src="/grase/images/icons/cross.png" alt=""/>Delete User</button></form></td></tr>
</table>
{else}
<div class="errorPage" style="display: {if $error}block;{else}none;{/if}">Error in data, please correct and try again<br/><span id="errorMessage">{foreach from=$error item=msg}{$msg}<br/>{/foreach}</span> </div>
<table>
	<tr><td>Username</td><td>{$user.Username}</td></tr>
	<tr><td>Password</td><td><form method='post'> <input type="text" name="Password"/ value='' onkeyup="runPassword(this.value, 'newpassword');" />
	                        <div style="width: 200px;float: right;"> 
                                <div id="newpassword_text" ></div>
                                <div id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></div> 	
	
	<button type="submit" name="changepasswordsubmit" value="Change Password"><img src="/grase/images/icons/textfield_key.png" alt=""/>Change Password</button></form></td></tr>
	<tr><td>Group</td><td><form method='post'> {html_options name="Group" options=$Usergroups selected=$user.Group}<button type="submit" name="changegroupsubmit" value="Change Group">Change Group</button></form></td></tr>
	
	<tr><td>Comment</td><td><form method='post'> <input type="text" name="Comment" value='{$user.Comment}'/><button type="submit" name="changecommentsubmit" value="Change Comment">Change Comment</button></form></td></tr>

	<tr><td>Data Limit (Mb)</td><td><form method='post'> <input type="text" name="MaxMb" value='{$user.MaxMb}'/>{html_options name="MaxMb_" options=$Datacosts}<button type="submit" name="changedatalimitsubmit" value="Change Data Limit">Change Data Limit</button></form></td></tr>
	<tr><td {if ! $user.MaxMb}style="color: gray"{/if}>Add Data (Mb)</td><td><form method='post'> <input {if ! $user.MaxMb}disabled='disabled'{/if} type="text" name="AddMb" value=''/>{html_options name="AddMb_" options=$Datacosts}<button {if ! $user.MaxMb}disabled='disabled'{/if} type="submit" name="adddatasubmit" value="Add more Data">Add Data to Limit</button></form></td></tr>

	<tr><td>Time Limit (Mins)</td><td><form method='post'> <input type="text" name="MaxTime" value='{$user.MaxTime}'/>{html_options name="MaxTime_" options=$Timecosts}<button type="submit" name="changetimelimitsubmit" value="Change Time Limit">Change Time Limit</button></form></td></tr>
	<tr><td {if ! $user.MaxTime}style="color: gray"{/if}>Add Time (Mins)</td><td><form method='post'> <input {if ! $user.MaxTime}disabled='disabled'{/if} type="text" name="AddTime" value=''/>{html_options name="AddTime_" options=$Timecosts}<button {if ! $user.MaxTime}disabled='disabled'{/if} type="submit" name="addtimesubmit" value="Add more Time">Add Time to limit</button></form></td></tr>

	<tr><td>Expiry (Automatic)<a class="helpbutton" onclick="ShowContent('helpbox','Expiry is based on the Group.<br\/>1 Month for visitors<br\/>3 Months for students<br\/>6 Months for staff and ministry');" ><img src="/grase/images/icons/help.png" alt=""/></a></td><td>{$user.FormatExpiration}
<!--<form method='post'> {html_select_date disabled='disabled' prefix="Expirydate_" time=$user.Expiration end_year="+1" year_empty='' month_empty='' day_empty=''} <input type="submit" name="changeexpirysubmit" value="Change Expiry"/></form>-->
</td></tr>
	<tr>&nbsp;</tr>
	<tr><td colspan='2' style='color: red'>If you really want to delete a user, please type exactly "Y<span style="display:none;">PLEASE DON'T COPY AND PASTE</span>es, I want to delete this use<span style="display:none;">PLEASE DON'T COPY AND PASTE</span>r" in the below box then click "Delete User"</td><tr/>
	<tr><td></td><td><form method='post'><input size='30' type="text" name="DeleteUser"/><button class="negative" type="submit" name="deleteusersubmit" value="Delete User"><img src="/grase/images/icons/cross.png" alt=""/>Delete User</button></form></td></tr>
</table>
</div>
{/if}

{include file="footer.tpl"}
