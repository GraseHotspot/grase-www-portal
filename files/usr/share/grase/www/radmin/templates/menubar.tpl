<div id="menubar" class="buttons ui-widget-content">

<div id="menucontainer">
{if $LoggedInUsername}<span id="loggedinusername">{t escape=no username=$LoggedInUsername}Logged in as <b>%1</b>{/t}</span>{/if}

<ul>
{foreach from=$MenuItems key=menuitemid item=menuitem name=toploop}
    <li class="{if $menuitemid == "logout"}negative{/if}"><span class="collapse"><span class="ui-icon ui-icon-circle-triangle-s">-</span></span><span class="expand"><span class="ui-icon ui-icon-circle-triangle-w">+</span></span><a id="{$menuitemid}" 
                href="{$menuitem.href}" class="ui-state-default ui-corner-top {if $activepage == $menuitemid}ui-state-active ui-state-highlight{/if} topmenu" >{$menuitem.label}</a>
        {if $menuitem.submenu}
            <ul id="submenu{$menuitemid}" class="submenu">
            {foreach from=$menuitem.submenu item=submenuitem key=submenuid name=subloop}
                <li id="{$submenuid}"
                class="

                "
                
                >
                <a href="{$submenuitem.href}" class="ui-state-default
                                {if $activepage == $submenuid}ui-state-active ui-state-highlight{/if} 
                                "
                >{$submenuitem.label}</a>
                </li>
            {/foreach}
            </ul>
        {/if}
    </li>
{/foreach}
</ul>

{if $helptext}<div id="helplinkbox" class="buttons">
<span id="helplink" class="positive">
<a class="helpbutton  ui-state-highlight" title='{$helptext}'><span class="ui-icon ui-icon-info"><img src="/grase/images/icons/help.png" alt=""/></span>Help</a>
</span>
</div>
{/if}
</div>

<div class="clear_both">&nbsp;</div>
</div>
