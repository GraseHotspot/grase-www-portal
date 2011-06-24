{include file="header.tpl" Name="Create Computer Account" activepage="createmachine"}

<div id="newMachineForm">
<h2>{t}Create Computer Account{/t}</h2>

<form method='post' name='newmachine' action='?' class='generalForm'>

<div>
    <label for='mac'>{t}MAC Address{/t}</label>
    <input type="text" name="mac" id="mac" value="{$machine.mac}"/>
    <span id="macInfo">Computer Hardware Address <a class="helpbutton  ui-icon ui-icon-info" title="{t}The MAC address is the network hardware address of the computer. It needs to be of the format XX-XX-XX-XX-XX-XX where XX is a hex number, typed in all capitals{/t}"><img src="/grase/images/icons/help.png" alt=""/></a></span>
</div>

<div>
    <label for='Comment'>{t}Comment{/t}</label>
    <input type="text" name="Comment" id="Comment" value="{$machine.Comment}"/>
    <span id='CommentInfo'>{t}Identify the computer. i.e. "Bob's computer"{/t}</span>
</div>

    <span>{t}When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left){/t}<br/><a class="helpbutton ui-icon ui-icon-info" title="{t}Computer accounts are intended to be used for computers that need internet access at all times. For example, an office computer. Computer accounts login automatically as soon as they access the network, this will normally occur at startup. If a limit is set, when the limit is reached the computer will revert to the normal login screen for internet access. Computer accounts can not be edited to add more data or time, only deleted.{/t}"><img src="/grase/images/icons/help.png" alt=""/> {t}It is not recommended setting a time or data limit for computer accounts.{/t}</a></span>

<div>
    <label for='MaxMb'>{t}Data Limit (MiB){/t}</label>
    {html_options name="Max_Mb" options=$Datacosts selected=$machine.Max_Mb}
    <span class="form_or">{t}OR{/t}</span>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$machine.MaxMb}' title="{t}Type your own Mb Limit{/t}"/>
    <span id='Max_MbInfo'>Choose a Data Limit OR Type your own value</span>
</div>
<div>
    <label for='MaxTime'>{t}Time Limit (Minutes){/t}</label>
    {html_options name="Max_Time" options=$Timecosts selected=$machine.Max_Time}
    <span class="form_or">{t}OR{/t}</span>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$machine.MaxTime}' title="{t}Type your own Time Limit{/t}"/>
    <span id='Max_TimeInfo'>{t}Choose a Time Limit OR Type your own value{/t}</span>
</div>

        <button type="submit" name="newmachinesubmit" value="{t}Create New Computer Account{/t}"><img src="/grase/images/icons/tick.png" alt=""/>{t}Create New Computer Account{/t}</button>

</form>
</div>

{include file="footer.tpl"}
