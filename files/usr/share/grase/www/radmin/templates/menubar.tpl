<div id="menubar" class="buttons">
{if $LoggedInUsername}<span id="loggedinusername">Logged in as&nbsp;<b>{$LoggedInUsername}</b></span>{/if}
{foreach from=$MenuItems key=menuitemid item=menuitem}<a id="{$menuitemid}" {if $activepage == $menuitemid}class="activemenuitem"{/if} href="{$menuitem.href}" {if $menuitemid == "logout"}class="negative"{/if} >{$menuitem.label}</a><br/>{/foreach}
{if $helptext}<div id="helplinkbox" class="buttons">
<span id="helplink" class="positive">
<a class="helpbutton" onclick="ShowContent('helpbox','{$helptext}');" ><img src="/images/icons/help.png" alt=""/>Help</a>
</span>
</div>
{/if}
<div class="clear_both">&nbsp;</div>
</div>
