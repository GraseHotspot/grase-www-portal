{include file="header.tpl" Name="Useful Links" activepage="links"}
<ul>
{foreach from=$links item=link key=id}
	<li><a href='{$link.href}'>{$link.label}</a></li>
{/foreach}
</ul>

{include file="footer.tpl"}
