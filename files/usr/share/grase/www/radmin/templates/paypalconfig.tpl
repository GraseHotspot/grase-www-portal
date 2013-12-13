{include file="header.tpl" Name="PayPal Settings" activepage="paypalconfig" helptext="Use this page to change PayPal Payment Settings"}

<h2>{t}PayPal Settings{/t}</h2>

<p>{t}These settings are used for the PayPal payment gateway to allow your users to purchase internet accounts online with a Credit Card or PayPal account. You will need to set which groups are allowed to be purchased on the Group Config page.{/t}</p>

<div id="PayPalConfigForm">
<form method="post" action="?" class="generalForm">

    {foreach from=$singlepaypaloptions item=attributes key=option}
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


    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
