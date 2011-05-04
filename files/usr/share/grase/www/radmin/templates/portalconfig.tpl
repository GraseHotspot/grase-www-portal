{include file="header.tpl" Name="Portal Config" activepage="portalconfig" helptext="Use this page to change portal Settings"}

<h2>{t}Portal Config{/t}</h2>



<div id="PortalConfigForm">
<form method="post" action="" class="generalForm">

    {foreach from=$singlechillioptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        <input type="text" name="{$option}" value='{$attributes.value}'/>
        <span id="{$option}Info"><span class="helpbutton ui-icon ui-icon-info" title="Chilli Option: {$option}">({$option})</span> {$attributes.description}</span>
    </div>
    {/foreach}

<p class="nojshelp">{t}For each of the following items, if you need multiple values you can submit the form and it will append a blank input below the last valid value{/t}<p>
    
    {foreach from=$multichillioptions item=attributes key=option name=multiloop}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        {foreach from=$attributes.value item=attribute name=attributeloop}
        <div class="jsmultioption"><input type="text" name="{$option}[]" value='{$attribute}'/><span class="jsremove"></span></div>
        {/foreach}
        <div class="jsmultioption"><input type="text" name="{$option}[]" value=''/><span class="jsadd"></span></div>
        <span id="{$option}Info"><span class="helpbutton ui-icon ui-icon-info" title="Chilli Option: {$option}">({$option})</span> {$attributes.description}</span>

    </div>
    {/foreach}    

    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
