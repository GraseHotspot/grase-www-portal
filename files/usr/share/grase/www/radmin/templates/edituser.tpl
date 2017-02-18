{include file="header.tpl" Name="Edit User" activepage="users"}

<div id="edituserForm">
{if $user.isComputer}
<h2>{t}Edit Computer Account{/t}</h2>
{else}
<h2>{t}Edit User{/t}</h2>
{/if}

<form method='post' name='edituser' action='' class='generalForm'>
<div>
    <label for='Username'>{t}Username{/t}</label>
    <input disabled='disabled' type="text" name="Username" value='{$user.Username|escape}'/>
    <span id="UsernameInfo">&nbsp;</span>
    <a target='_blank' href='export.php?format=html&user={$user.Username|escape}'>Print Ticket</a>
</div>

{if ! $user.isComputer}
<div>
    <label for='Password'>{t}Password{/t}</label>
    <input type="text" name="Password" value='' onkeyup="runPassword(this.value, 'newpassword');" />
    <span id='PasswordInfo'>{t}Choose a secure password for the user. Leave blank to not change{/t}</span>
                                <span id="newpassword_text" ></span>
                                <span id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></span> 
</div>
{/if}

<div>
    <label for='Group'>Group</label>
    {*{html_options name="Group" options=$Usergroups selected=$user.Group}   *}
    {html_options name="Group" id="Group" options=$groups selected=$user.Group}      
    <span id='GroupInfo'>{t}Choose the users group (Expiry is based on the user group){/t}</span>
    
<br/>{include file="grouppropertiesinfo.tpl"}

</div>


<div>
    <label>Expiration</label>
{if $user.Expiration == '--'}<strong>{t}This user account never expires{/t}</strong>
{else}{* Check if ExpirationTimestamp is passed *}
    {if $user.ExpirationTimestamp > $smarty.now}{$user.FormatExpiration}
    {else}<strong>{t}This user account has expired{/t}</strong><br/>{$user.FormatExpiration}<br/>
    <button type="submit" name="unexpiresubmit">{t}Reset expiry{/t}</button>
    {/if}
{/if}
</div>

<div>
    <label>Expire After First Login</label>
    {if $user.ExpireAfter == ''}<strong>{t}No Expire After Set{/t}</strong>
    {else}
        {$user.ExpireAfter}
    {/if}
</div>



<div>
    <label for='Comment'>{t}Comment{/t}</label>
    <input type="text" name="Comment" value='{$user.Comment|escape}' autofocus="autofocus"/>
    <span id='CommentInfo'>{t}A comment about the user{/t}</span>
</div>

<div>
    <label for='LockReason'>{t}Account Lock Reason{/t}</label>
    <input type="text" name="LockReason" value='{$user.LockReason|escape}'/>
    <span id='LockReasonInfo'>{t}Enter a reason in here to lock the users account. Clear the reason to unlock the account{/t}</span>
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
    {html_options name="Add_Mb" options=$GroupDatacosts selected=$user.Add_Mb}
    <span class="Add_MbInfo">{t}Add to the Data Limit (Will ignore changes made above){/t}</span>    
</div>
<div>
    <label for='MaxTime'>{t}Time Limit (Minutes){/t}</label>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="{t}Type your own Time Limit{/t}"/>
    <span id='MaxTimeInfo'>{t}Change the Time Limit{/t}</span>
</div>
<div>
    <label for='Add_Time'>{t}Add Time to Limit (Minutes){/t}</label>
     {html_options name="Add_Time" options=$GroupTimecosts selected=$user.Add_Time}
    <span class="Add_TimeInfo">{t}Add to the Time Limit (Will ignore changes made above){/t}</span>    
</div>

<button type="submit" name="updateusersubmit" value="{t}Update User Details{/t}"><img src="/grase/images/icons/tick.png" alt=""/>{t}Update User Details{/t}</button>
</form>

{if $user.isComputer}
<h3>{t}Delete Computer Account{/t}</h3>
{else}
<h3>{t}Delete User{/t}</h3>
{/if}
<form method='post' name='deleteuser' action='' class='generalForm'>

{t}User accounts are automatically deleted 2 months after expiry. Only accounts with zero usage should be manually deleted to prevent errors in the reports or statistics.{/t}<br/>
    <button class="negative" type="submit" name="deleteusersubmit" value="{t}Delete User{/t}" onClick="return confirm('{t}Are you sure you want to delete this user?{/t}')"><img src="/grase/images/icons/cross.png" alt=""/>{t username=$user.Username}Delete User %1{/t}</button>


</form>




{include file="footer.tpl"}
