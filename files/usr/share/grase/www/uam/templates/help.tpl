{include file="header.tpl" Name="Help" activepage="help"}

<div id="page">
<h1>{$Location} Hotspot - {t}Help{/t}</h1>

<p><a href="hotspot">{t}Return to Welcome Page{/t}</a></p>
{if $tpl_helptext}
<div id="tpl_helptext">
    {$tpl_helptext}
</div>
{/if}

{include file="footer.tpl"}
