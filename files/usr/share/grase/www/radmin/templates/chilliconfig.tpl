{include file="header.tpl" Name="Coova Chilli Settings" activepage="chilliconfig" helptext="Use this page to change Coova Chilli Settings"}

<h2>{t}Coova Chilli Settings{/t}</h2>

<p>{t}These settings are used by Coova Chilli. Coova Chilli only reloads it's config roughly once an hour, or when it's restarted.{/t} {t}If you change the MAC Auth Password, no computer accounts will be able to login until Coova Chilli reloads it's config, or is manually restarted.{/t}</p>

<p>{t one=$chilliconfigstatus}Chilli Config last updated %1{/t}<br/>
{t one=$lastconfigstatus}Portal Config last updated %1{/t}</p>

<div id="ChilliConfigForm">
<form method="post" action="?" class="generalForm">

    {foreach from=$singlechillioptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        <input type="text" name="{$option}" id="{$option}" value='{$attributes.value}'/>
        <span id="{$option}Info"><span class="helpbutton ui-icon ui-icon-info" title="Chilli Option: {$option}">({$option})</span> {$attributes.description}</span>
    </div>
    {/foreach}

<p class="nojshelp">{t}For each of the following items, if you need multiple values you can submit the form and it will append a blank input below the last valid value{/t}<p>
    
    {foreach from=$multichillioptions item=attributes key=option name=multiloop}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        {foreach from=$attributes.value item=attribute name=attributeloop}
        <div class="jsmultioption"><input type="text" name="{$option}[]" value='{$attribute}'/>
            <span class="jsremove">
                <span class="jsremovebutton"></span>
                <span class="jsaddremovetext" id="addtext"></span>
            </span>                 
        </div>
        {/foreach}
        <div class="jsmultioption"><input type="text" name="{$option}[]" id="{$option}" value=''/>
            <span class="jsadd">
                <span class="jsaddbutton"></span>
                <span class="jsaddremovetext" id="addtext"></span>
            </span>            
        </div>
        <span id="{$option}Info"><span class="helpbutton ui-icon ui-icon-info" title="Chilli Option: {$option}">({$option})</span> {$attributes.description}</span>

    </div>
    {/foreach}    

    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
