{include file="header.tpl" Name="Groups" activepage="groups"}

<h2>{t}Group Config{/t}</h2>
<p>{t}Group expiry needs to be in a format understood by the PHP Function, strtotime. For example, "+1 month" will set an expiry for 1 month from when the account is created. It is recommended that all expiries are relative to "now", so that the expiry is applied from when the user account is created. Using the format of "+X Y" where X is a number and Y is one of day, week, month is fairly safe and should always work.{/t}</p>
<p>{t}Deleting a group won't delete it's users. Next time the user is edited it's group will become the default group unless a new group is selected.{/t}</p>
<div id="GroupConfigForm">
<form method="post" action="" class="generalForm">




    <div>
        <label for='groupname'>{t}Group Name and Expiry{/t}</label>
        
    {foreach from=$groups item=expiry key=groupname}        
        <div class="jsmultioption">
            <input type="text" name="groupname[]" value='{$groupname}'/>
            <input type="text" name="groupexpiry[]" value='{$expiry}'/><span class="jsremove"></span></div>
    {/foreach}
        <div class="jsmultioption">
            <input type="text" name="groupname[]" value=''/>
            <input type="text" name="groupexpiry[]" value=''/><span class="jsadd"></span></div>
        <span id="groupsInfo"></span>

    </div>

    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
