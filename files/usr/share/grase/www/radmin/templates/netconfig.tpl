{include file="header.tpl" Name="Network Settings" activepage="netconfig" helptext="Use this page to change Network Settings"}

<h2>{t}Network Settings{/t}</h2>

<p>{t}These settings are used by Coova Chilli and Dnsmasq. Both Coova Chilli and Dnsmasq will need to be reloaded when these settings change. A cron job will automatically check for changes every 5 minutes and reload these daemons if needed.{/t}</p>

<p>{t one=$networkconfigstatus}Network Config last reloaded %1{/t}<br/>
{t one=$lastnetworkconfigstatus}Network Config last updated %1{/t}</p>

<div id="NetworkConfigForm">
<form method="post" action="?" class="generalForm">

    {foreach from=$singlenetworkoptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        <input {inputtype type=$attributes.type value=$attributes.value} name="{$option}" id="{$option}" {if $attributes.required}required{/if}/>
        <span id="{$option}Info">{$attributes.description}</span>
    </div>
    {/foreach}

<p class="nojshelp">{t}For each of the following items, if you need multiple values you can submit the form and it will append a blank input below the last valid value{/t}<p>
    
    {foreach from=$multinetworkoptions item=attributes key=option name=multiloop}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        {foreach from=$attributes.value item=attribute name=attributeloop}
        <div class="jsmultioption">
                <input {inputtype type=$attributes.type value=$attribute} name="{$option}[]" {if $attributes.required}required{/if}/>
            <span class="jsremove">
                <span class="jsremovebutton"></span>
                <span class="jsaddremovetext" id="addtext"></span>
            </span>                 
        </div>
        {/foreach}
        <div class="jsmultioption">
                <input {inputtype type=$attributes.type} name="{$option}[]" {if $attributes.required}required{/if}/>
            <span class="jsadd">
                <span class="jsaddbutton"></span>
                <span class="jsaddremovetext" id="addtext"></span>
            </span>            
        </div>
        <span id="{$option}Info">{$attributes.description}</span>

    </div>
    {/foreach}    

    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
