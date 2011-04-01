{include file="header.tpl" Name="Edit User" activepage="users"}

<div id="edituserForm">
<h2>Edit User</h2>

{if $user.Group eq MACHINE_GROUP_NAME}
<div class="errorPage" style="display: block;"> <span id="errorMessage">Machine Account Locked (Only comments can be changed){if $error}<br/>{foreach from=$error item=msg}{$msg}<br/>{/foreach}{/if}</span> </div>

<table>
	<tr><td>Username</td><td>{$user.Username}</td></tr>
	<!--<tr><td>Comment</td><td>{$user.Comment}</td></tr>	-->
	<tr><td>Group</td><td>{$user.Group}</td></tr>
	<tr><td>Data Limit (MiB)</td><td>{$user.MaxMb}</td></tr>
	<tr><td>Expiry</td><td>{$user.Expiration}</td></tr>
	
	<tr><td>Comment</td><td><form method='post'> <input type="text" name="Comment" value='{$user.Comment}'/><button type="submit" name="changecommentsubmit" value="Change Comment">Change Comment</button></form></td></tr>
</table>
<table>	
<tr><td colspan='2' style='color: red'>If you really want to delete this machine account, please type exactly "Y<span style="display:none;">PLEASE DON'T COPY AND PASTE</span>es, I want to delete this use<span style="display:none;">PLEASE DON'T COPY AND PASTE</span>r" in the below box then click "Delete User"</td><tr/>
	<tr><td></td><td><form method='post'><input size='30' type="text" name="DeleteUser"/><button class="negative" type="submit" name="deleteusersubmit" value="Delete User"><img src="/grase/images/icons/cross.png" alt=""/>Delete User</button></form></td></tr>
</table>
{else}

<form method='post' name='edituser' action='' class='generalForm'>
<div>
    <label for='Username'>Username</label>
    <input disabled='disabled' type="text" name="Username" value='{$user.Username}'/>
    <span id="UsernameInfo">&nbsp;</span>
</div>

<div>
    <label for='Password'>Password</label>
    <input type="text" name="Password" value='' onkeyup="runPassword(this.value, 'newpassword');" />
    <span id='PasswordInfo'>Choose a secure password for the user. Leave blank to not change</span>
                                <span id="newpassword_text" ></span>
                                <span id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></span> 
</div>
<div>
    <label for='Group'>Group</label>
    {html_options name="Group" options=$Usergroups selected=$user.Group}    
    <span id='GroupInfo'>Choose the users group (Expiry is based on the user group)</span>
</div>
    
<div>
    <label for='Comment'>Comment</label>
    <input type="text" name="Comment" value='{$user.Comment}'/>
    <span id='CommentInfo'>A comment about the user</span>
</div>

    <span>When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left)<br/>
    A limit of 0 does not mean unlimited, it will immediately lock the user out. To have an unlimited user, the user must be created without any limits.</span>

<div>
    <label for='MaxMb'>Data Limit (MiB)</label>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb}' title="Type your own Mb Limit"/>
    <span id='MaxMbInfo'>Change the Data Limit</span>
</div>
<div>
    <label for='Add_Mb'>Add Data to Limit (MiB)</label>
    {html_options name="Add_Mb" options=$Datacosts selected=$user.Add_Mb}
    <span class="Add_MbInfo">Add to the Data Limit (Will ignore changes made above)</span>    
</div>
<div>
    <label for='MaxTime'>Time Limit (Minutes)</label>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="Type your own Time Limit"/>
    <span id='MaxTimeInfo'>Change the Time Limit</span>
</div>
<div>
    <label for='Add_Time'>Add Time to Limit (Minutes)</label>
     {html_options name="Add_Time" options=$Timecosts selected=$user.Add_Time}
    <span class="Add_TimeInfo">Add to the Time Limit (Will ignore changes made above)</span>    
</div>

<button type="submit" name="updateusersubmit" value="Update User Details"><img src="/grase/images/icons/tick.png" alt=""/>Update User Details</button>
</form>

<form method='post' name='deleteuser' action='' class='generalForm'>

If you really want to delete a user, please type exactly "Yes, I want to delete this user" in the below box then click "Delete User"<br/>
<div>
    <label for='DeleteUser'>Delete the User</label>
    <input type="text" id="DeleteUserConfirm" name="DeleteUser"/>
    <span class="DeleteUserInfo"> </span>    
</div>

<button class="negative" type="submit" name="deleteusersubmit" value="Delete User"><img src="/grase/images/icons/cross.png" alt=""/>Delete User</button>
</form>


{/if}

{include file="footer.tpl"}
