{include file="header.tpl" Name="Create Tickets" activepage="createtickets"}

{if $listbatches}
<form id='batchactions' method='POST'>
<table>
<thead>
        <th>{t}Batch ID{/t}</th>
        <th>{t}Date Created{/t}</th>
        <th>{t}Created By{/t}</th>
        <th>{t}Number of Tickets{/t}</th>
        <th>{t}Comment{/t}</th>
</thead>
{foreach from=$listbatches item=batch}
<tr>
        <td><input type='checkbox' name='selectedbatches[]' value='{$batch.batchID}'/>{$batch.batchID}</td>
        <td>{$batch.createTime}</td>
        <td>{$batch.createdBy}</td>
        <td>{$batch.numTickets}</td>
        <td>{$batch.comment}</td>
</tr>
{/foreach}
</table>
<button name='batchesprint'>Print selected Batches</button>
<button name='batchesexport'>Export selected Batches (CSV)</button>
<button class="negative" type="submit" name="batchesdelete" onClick="return confirm('{t}Are you sure you want to delete the selected batches and all users in them?{/t}')"><img src="/grase/images/icons/cross.png" alt=""/>Delete selected Batches</button>
</form>
{/if}

{if $last_batch}<a href="printnewtickets?batch={$last_batch}" class="printlink" target="tickets">Print Last Batch of Tickets</a>{/if}


<div id="createticketsForm">
<h2>Create Tickets</h2>


<form method='post' id='newticketsform' action='?' class='generalForm'>

<div>
    <label for='numberoftickets'>Number of Tickets</label>
    <input type="number" min="1" max="1000" name="numberoftickets" id="numberoftickets"  value='{$user.numberoftickets}' {if !$createdusers}autofocus="autofocus"{/if} required="required"/>
    <span id="numberofticketsInfo">Maximum of 1000 tickets per batch</span>
</div>
<div>
    <label for='Group'>Group</label>
    {html_options name="Group" id="Group" options=$groups selected=$user.Group}
    <span id='GroupInfo'>Choose the users group (Expiry is based on the user group)</span>
    <br/>{include file="grouppropertiesinfo.tpl"}
</div>
<div>
    <label for='Comment'>Comment</label>
    <input type="text" name="Comment" id="Comment" value='{$user.Comment}'/>
    <span id='CommentInfo'>A comment that is applied to all tickets</span>
</div>

    <p><span>{t}When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left){/t}<br/>
    {t}A limit of 0 does not mean unlimited, it will immediately lock the user out. To have an unlimited user, the user must be created without any limits.{/t}<br/><strong>{t}If a limit is not set here, but is defined for the group, then the group limit will apply{/t}</strong></span></p>

<div>
    <label for='MaxMb'>Data Limit (MiB)</label>
    {html_options name="Max_Mb" options=$Datacosts selected=$user.Max_Mb}
    <span class="form_or">OR</span>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb}' title="Type your own Mb Limit"/>
    <span id='Max_MbInfo'>Choose a Data Limit OR Type your own value</span>
</div>
<div>
    <label for='MaxTime'>Time Limit (Minutes)</label>
    {html_options name="Max_Time" options=$Timecosts selected=$user.Max_Time}
    <span class="form_or">OR</span>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="Type your own Time Limit"/>
    <span id='Max_TimeInfo'>Choose a Time Limit OR Type your own value</span>
</div>

       <p><button type="submit" name="createticketssubmit" value="Create Tickets"><img src="/grase/images/icons/tick.png" alt=""/>{t}Create Tickets{/t}</button></p>

</form>
</div>




{include file="footer.tpl"}
