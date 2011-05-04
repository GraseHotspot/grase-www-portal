<div id="menubar" class="buttons ui-widget-content">

<div id="menucontainer">
{if $LoggedInUsername}<span id="loggedinusername">{t escape=no username=$LoggedInUsername}Logged in as <b>%1</b>{/t}</span>{/if}
{foreach from=$MenuItems key=menuitemid item=menuitem}
<a id="{$menuitemid}" class="
{if $activepage == $menuitemid}ui-state-active{/if} 
{if $menuitemid == "logout"}negative{else}
ui-state-default {/if}"
href="{$menuitem.href}"  >{$menuitem.label}</a><br/>{/foreach}

{if $helptext}<div id="helplinkbox" class="buttons">
<span id="helplink" class="positive">
<a class="helpbutton  ui-state-highlight" title='{$helptext}'><span class="ui-icon ui-icon-info"><img src="/grase/images/icons/help.png" alt=""/></span>Help</a>
</span>
</div>
{/if}
</div>

<div class="clear_both">&nbsp;</div>
</div>
