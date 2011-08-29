{include file="header.tpl" Name="Portal Settings" activepage="loginconfig" helptext="Use this page to change login page Settings"}

<h2>{t}Login Settings{/t}</h2>

<div id="LoginConfigForm">
<form method="post" action="?" class="generalForm">

    {foreach from=$singleloginoptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        {if $attributes.type != 'bool'}
            <input type="text" name="{$option}" id="{$option}" value='{$attributes.value}'/>
        {else}
            <input type="checkbox" name="{$option}" id="{$option}" {if $attributes.value == "TRUE"}checked="checked"{/if}'/>
        {/if}
        <span id="{$option}Info">{$attributes.description}</span>
    </div>
    {/foreach}

<p class="nojshelp">{t}For each of the following items, if you need multiple values you can submit the form and it will append a blank input below the last valid value{/t}<p>
    
    {foreach from=$multiloginoptions item=attributes key=option name=multiloop}
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
        <span id="{$option}Info">{$attributes.description}</span>

    </div>
    {/foreach}    

    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
