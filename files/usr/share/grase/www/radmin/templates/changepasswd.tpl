{include file="header.tpl" Name="Admin Users" activepage="passwd" helptext="Use this page to create and delete Administration users. Adminstration users are only for this $Application interface, and are different to the internet users."}

<h2>{t}Admin Users{/t}</h2>
<div id="passwdChangeForm">


<form method='post' id='passwdChange' action='?' class='generalForm'> 
<h3>{t}Change Password{/t}</h3>
<div>
    <label for='Username'>{t}Username{/t}</label>
    <input disabled='disabled' type="text" name="Username" id="Username" value='{$LoggedInUsername}'/>
    <span id="UsernameInfo"> </span>
</div>

<div>
    <label for='OldPassword'>{t}Old Password{/t}</label>
    <input type="password" name="OldPassword" id="OldPassword" value='' class="autoDisable" required="required"/>
    <span id="OldPasswordInfo"> </span>
</div>

<div>
    <label for='NewPassword'>{t}New Password{/t}</label>
    <input type="password" name="NewPassword" id="NewPassword" value='' class="autoDisable" required="required"/>
    <span id="NewPasswordInfo"> </span>
</div>

<div>
    <label for='ConfirmPassword'>{t}Confirm New Password{/t}</label>
    <input type="password" name="ConfirmPassword" id="ConfirmPassword" value='' class="autoDisable" required="required"/>
    <span id="ConfirmPasswordInfo"> </span>
</div>
<div>
    <button type="submit" name="changepasswordsubmit" value="{t}Change Password{/t}"><img src="/grase/images/icons/textfield_key.png" alt=""/>{t}Change Password{/t}</button>
    <span>&nbsp;</span>
</div>
</form> 
</div>

<div id="AddAdminUserForm">

<form method='post' id='AddUser' action='?' class='generalForm'>
<h3>{t}Create new Admin User{/t}</h3>

<div>
    <label for='newUsername'>{t}Username{/t}</label>
    <input type="text" name="newUsername" id="newUsername" value='' class="autoDisable" required="required"/>
    <span id="newUsernameInfo"> </span>
</div>

<div>
    <label for='newPassword'>{t}Password{/t}</label>
    <input type="password" name="newPassword" id="newPassword" value='' class="autoDisable" required="required"/>
    <span id="newPasswordInfo"> </span>
</div>

<div>
    <button type="submit" name="addadminusersubmit" class="positive" value="Add new Admin User"><img src="/grase/images/icons/tick.png" alt=""/>{t}Create new Admin User{/t}</button>
    <span>&nbsp;</span>
</div>

</form>
</div>

<div id="DeleteAdminUserForm">
<h3>{t}Delete Admin User{/t}</h3>

{foreach from=$adminusers item=user}
<form method='post' id='DeleteUser{$user}' action='?' class='generalForm'>
<div>
    <label id='delete{$user}'>{$user}{if $user == 'support'}&nbsp;<a class="helpbutton" title='{t}Deleting or modifying the support user will prevent remote assistance.{/t}' ><img src="/grase/images/icons/help.png" alt=""/></a>{/if}</label>
    {if $LoggedInUsername != $user}
        <input type="hidden" value="{$user}" name="deleteusername"/>
        <span><button class="negative" type="submit" name="deleteadminusersubmit" value="Delete {$user}" onclick="return confirm('{t user=$user}Are you sure you want to delete %1?{/t}');"><img src="/grase/images/icons/cross.png" alt=""/>{t user=$user}Delete %1{/t}</button></span>
    {else}
        <span>{t}Cannot delete current user{/t}</span>
    {/if}
    

</div>
    </form>
{/foreach}

</div>

{include file="footer.tpl"}
