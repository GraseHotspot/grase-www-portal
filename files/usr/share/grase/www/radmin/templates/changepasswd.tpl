{include file="header.tpl" Name="Change Password" activepage="passwd" helptext="Use this page to create and delete Administration users. Adminstration users are only for this $Application interface, and are different to the internet users."}

<div id="passwdChangeForm">


<form method='post' id='passwdChange' action='' class='generalForm'> 
<h2>Change Password</h2>
<div>
    <label for='Username'>Username</label>
    <input disabled='disabled' type="text" name="Username" value='{$LoggedInUsername}'/>
    <span id="UsernameInfo"> </span>
</div>

<div>
    <label for='OldPassword'>Old Password</label>
    <input type="password" name="OldPassword" value='' class="autoDisable"/>
    <span id="OldPasswordInfo"> </span>
</div>

<div>
    <label for='NewPassword'>New Password</label>
    <input type="password" name="NewPassword" value='' class="autoDisable"/>
    <span id="NewPasswordInfo"> </span>
</div>

<div>
    <label for='ConfirmPassword'>Confirm New Password</label>
    <input type="password" name="ConfirmPassword" value='' class="autoDisable"/>
    <span id="ConfirmPasswordInfo"> </span>
</div>
<div>
    <button type="submit" name="changepasswordsubmit" value="Change Password"><img src="/grase/images/icons/textfield_key.png" alt=""/>Change Password</button>
    <span>&nbsp;</span>
</div>
</form> 
</div>

<div id="AddAdminUserForm">

<form method='post' id='AddUser' action='' class='generalForm'>
<h2>Create new Admin User</h2>

<div>
    <label for='newUsername'>Username</label>
    <input type="text" name="newUsername" value='' class="autoDisable"/>
    <span id="newUsernameInfo"> </span>
</div>

<div>
    <label for='newPassword'>Password</label>
    <input type="password" name="newPassword" value='' class="autoDisable"/>
    <span id="newPasswordInfo"> </span>
</div>

<div>
    <button type="submit" name="addadminusersubmit" class="positive" value="Add new Admin User"><img src="/grase/images/icons/tick.png" alt=""/>Create new Admin User</button>
    <span>&nbsp;</span>
</div>

</form>
</div>

<div id="DeleteAdminUserForm">
<h2>Delete Admin User</h2>

{foreach from=$adminusers item=user}
<form method='post' id='DeleteUser{$user}' action='' class='generalForm'>
<div>
    <label for='delete{$user}'>{$user}{if $user == 'support'}&nbsp;<a class="helpbutton" title='Deleting or modifying the support user will prevent remote assistance.' ><img src="/grase/images/icons/help.png" alt=""/></a>{/if}</label>
    {if $LoggedInUsername != $user}
        <input type="hidden" value="{$user}" name="deleteusername"/>
        <span><button class="negative" type="submit" name="deleteadminusersubmit" value="Delete {$user}" onclick="return confirm('Are you sure you want to delete {$user}?');"><img src="/grase/images/icons/cross.png" alt=""/>Delete {$user}</button></span>
    {else}
        <span>Cannot delete current user</span>
    {/if}
    

</div>
    </form>
{/foreach}

</div>

{include file="footer.tpl"}
