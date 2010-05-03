{include file="header.tpl" Name="Change Password" activepage="passwd" helptext="Use this page to create and delete Administration users. Adminstration users are only for this $Application interface, and are different to the internet users."}

<div id="passwdChangeForm">
<h2>Change Password</h2>
{if $error_passwd}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_passwd}</span> </div>{/if}
<form method='post' id='passwdChange' action=''> 
<table>
<tr><td>Username</td><td><b>{$LoggedInUsername}</b></td></tr>
<tr><td>Old Password</td><td><input type="password" name="OldPassword" value=''/></td></tr>
<tr><td>New Password</td><td><input type="password" name="NewPassword" value=''/></td></tr>
<tr><td>Confirm New Password</td><td><input type="password" name="ConfirmPassword" value=''/></td></tr>
<tr><td></td><td><button type="submit" name="changepasswordsubmit" value="Change Password"><img src="/images/icons/textfield_key.png" alt=""/>Change Password</button></td></tr>
</table>
</form> 
</div>

<div id="AddAdminUserForm">
<h2>Create new Admin User</h2>
{if $error_user}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_user}</span> </div>{/if}
<form method='post' id='AddUser' action=''>
<table>
<tr><td>Username</td><td><input type="text" name="newUsername" value=''/></td></tr>
<tr><td>Password</td><td><input type="password" name="newPassword" value=''/></td></tr>
<tr><td></td><td><button type="submit" name="addadminusersubmit" class="positive" value="Add new Admin User"><img src="/images/icons/tick.png" alt=""/>Create new Admin User</button></td></tr>
</table>
</form>
</div>

<div id="DeleteAdminUserForm">
<h2>Delete Admin User</h2>
{if $error_delete}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_delete}</span> </div>{/if}
<table>
{foreach from=$adminusers item=user}
<tr><td><label>{$user}{if $user == 'support'}&nbsp;<a class="helpbutton" onclick="ShowContent('helpbox','Deleting or modifying the support user will prevent remote assistance.');" ><img src="/images/icons/help.png" alt=""/></a>{/if}</label></td><td>{if $LoggedInUsername != $user}<form method='post' id='DeleteUser{$user}' action=''><p><input type="hidden" value="{$user}" name="deleteusername"/><button class="negative" type="submit" name="deleteadminusersubmit" value="Delete {$user}" onclick="return confirm('Are you sure you want to delete {$user}?');"><img src="/images/icons/cross.png" alt=""/>Delete {$user}</button></p></form>{else}Cannot delete current user{/if}</td></tr>
{/foreach}
</table>
</div>

{include file="footer.tpl"}
