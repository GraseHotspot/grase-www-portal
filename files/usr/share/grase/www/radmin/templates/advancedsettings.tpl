{include file="header.tpl" Name="Advanced Settings" activepage="advancedsettings" helptext="Use this page to see and change any setting, including hidden ones"}

<h2>{t}Advanced Settings{/t}</h2>

{t}All settings in the Radmin Settings table are shown here. Modifying settings here isn't recommended as no validation is done. This page allows you to see and modify hidden settings. Some settings may reset back to defaults if you try to set them to an incorrect value, others will just break.{/t}<br/>
<strong>{t}CAUTION: Some settings here are internal and modifying them will break things{/t}</strong>

<table>
    <colgroup>
        <col span="1" style="min-width: 6em; max-width: 10em;"/>
        <col span="1" style="width: auto;"/>
    </colgroup>
    <thead>
        <tr>
            <th>{t}Name{/t}</th>
            <th>{t}Value{/t}</th>
        </tr>
    </thead>
{foreach from=$allSettings item=value key=name}
    <tr  style="border-bottom: solid #000000 1px">
        <td>{$name}</td>
        <td>{$value}</td>
    </tr>
{/foreach}
</table>

<div id="AdvancedSettingsForm">
    <form method="post" action="?" class="generalForm">

        <label>{t}Setting name{/t}</label><input type="text" name="name"/><br/>
        <label>{t}Setting value{/t}</label><input type="text" name="value"/><br/>

        <button type="submit" name="submitAdvancedSettingsForm">{t}Save Setting{/t}</button>

    </form>

</div>

{include file="footer.tpl"}
