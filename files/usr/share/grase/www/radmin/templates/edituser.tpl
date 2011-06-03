{include file="header.tpl" Name="Edit User" activepage="users"}

<div id="edituserForm">
<h2>{t}Edit User{/t}</h2>

{if $user.Group eq MACHINE_GROUP_NAME}
<div class="errorPage" style="display: block;"> <span id="errorMessage">{t}Computer Account Locked (Only comments can be changed){/t}{if $error}<br/>{foreach from=$error item=msg}{$msg}<br/>{/foreach}{/if}</span> </div>

<table>
	<tr><td>{t}Username{/t}</td><td>{$user.Username}</td></tr>
	<!--<tr><td>{t}Comment{/t}</td><td>{$user.Comment}</td></tr>	-->
	<tr><td>{t}Group{/t}</td><td>{$user.Group}</td></tr>
	<tr><td>{t}Data Limit (MiB){/t}</td><td>{$user.MaxMb}</td></tr>
	<tr><td>{t}Expiry{/t}</td><td>{$user.Expiration}</td></tr>
	
	<tr><td>{t}Comment{/t}</td><td><form method='post'> <input type="text" name="Comment" value='{$user.Comment}'/><button type="submit" name="changecommentsubmit" value="{t}Change Comment{/t}">{t}Change Comment{/t}</button></form></td></tr>
</table>
<table>	
<tr><td colspan='2' style='color: red'>{t}If you really want to delete this machine account, please type exactly "Yes, I want to delete this user" in the below box then click "Delete User"{/t}<br/>
{t}User accounts are automatically deleted 2 months after expiry. Only unused accounts should be manually deleted to prevent errors in the reports or statistics.{/t}</td><tr/>
	<tr><td></td><td><form method='post'><input size='30' type="text" name="DeleteUser"/><button class="negative" type="submit" name="deleteusersubmit" value="{t}Delete User{/t}"><img src="/grase/images/icons/cross.png" alt=""/>{t}Delete User{/t}</button></form></td></tr>
</table>
{else}

<form method='post' name='edituser' action='' class='generalForm'>
<div>
    <label for='Username'>{t}Username{/t}</label>
    <input disabled='disabled' type="text" name="Username" value='{$user.Username}'/>
    <span id="UsernameInfo">&nbsp;</span>
</div>

<div>
    <label for='Password'>{t}Password{/t}</label>
    <input type="text" name="Password" value='' onkeyup="runPassword(this.value, 'newpassword');" />
    <span id='PasswordInfo'>{t}Choose a secure password for the user. Leave blank to not change{/t}</span>
                                <span id="newpassword_text" ></span>
                                <span id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></span> 
</div>
<div>
    <label for='Group'>Group</label>
    {html_options name="Group" options=$Usergroups selected=$user.Group}    
    <span id='GroupInfo'>{t}Choose the users group (Expiry is based on the user group){/t}</span>
</div>
    
<div>
    <label for='Comment'>{t}Comment{/t}</label>
    <input type="text" name="Comment" value='{$user.Comment}'/>
    <span id='CommentInfo'>{t}A comment about the user{/t}</span>
</div>

    <span>{t}When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left){/t}<br/>
    {t}A limit of 0 does not mean unlimited, it will immediately lock the user out. To have an unlimited user, the user must be created without any limits.{/t}</span>

<div>
    <label for='MaxMb'>{t}Data Limit (MiB){/t}</label>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb|displayLocales}' title="{t}Type your own Mb Limit{/t}"/>
    <span id='MaxMbInfo'>{t}Change the Data Limit{/t}</span>
</div>
<div>
    <label for='Add_Mb'>{t}Add Data to Limit (MiB){/t}</label>
    {html_options name="Add_Mb" options=$Datacosts selected=$user.Add_Mb}
    <span class="Add_MbInfo">{t}Add to the Data Limit (Will ignore changes made above){/t}</span>    
</div>
<div>
    <label for='MaxTime'>{t}Time Limit (Minutes){/t}</label>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="{t}Type your own Time Limit{/t}"/>
    <span id='MaxTimeInfo'>{t}Change the Time Limit{/t}</span>
</div>
<div>
    <label for='Add_Time'>{t}Add Time to Limit (Minutes){/t}</label>
     {html_options name="Add_Time" options=$Timecosts selected=$user.Add_Time}
    <span class="Add_TimeInfo">{t}Add to the Time Limit (Will ignore changes made above){/t}</span>    
</div>

<button type="submit" name="updateusersubmit" value="{t}Update User Details{/t}"><img src="/grase/images/icons/tick.png" alt=""/>{t}Update User Details{/t}</button>
</form>

<form method='post' name='deleteuser' action='' class='generalForm'>

{t}If you really want to delete a user, please type exactly "Yes, I want to delete this user" in the below box then click "Delete User"{/t}<br/>
{t}User accounts are automatically deleted 2 months after expiry. Only unused accounts should be manually deleted to prevent errors in the reports or statistics.{/t}<br/>
<div>
    <label for='DeleteUser'>{t}Delete the User{/t}</label>
    <input type="text" id="DeleteUserConfirm" name="DeleteUser"/>
    <span class="DeleteUserInfo"> </span>    
</div>

<button class="negative" type="submit" name="deleteusersubmit" value="{t}Delete User{/t}"><img src="/grase/images/icons/cross.png" alt=""/>{t}Delete User{/t}</button>
</form>


{/if}

{include file="footer.tpl"}
