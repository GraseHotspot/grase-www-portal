{include file="header.tpl" Name="Groups" activepage="groups"}

<h2>{t}Group Config{/t}</h2>
<p>{t escape=no}Group expiry needs to be in a format understood by the <a target="_blank" href="http://www.php.net/manual/en/function.strtotime.php">strtotime</a> PHP function.{/t}<br/>{t}For example, "+1 month" will set an expiry for 1 month from when the account is created. "+2 weeks", "+3 days" etc.{/t}</p>

<p>{t}Deleting a group won't delete it's users. Next time the user is edited it's group will become the default group unless a new group is selected.{/t}</p>
<div id="GroupConfigForm">
<form method="post" action="?" class="generalForm">




    <div>
        <label>{t}Group Name and Expiry{/t}</label>
        
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
