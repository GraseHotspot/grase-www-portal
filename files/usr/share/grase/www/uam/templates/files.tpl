{include file="header.tpl" Name="Welcome" activepage="welcome"}

<div id="page">
<h1>{$Location} - Free Downloads</h1><h2>{$path}</h2>

<ul>
{foreach from=$files item=file}
    <li><a href="{$file.name}">{$file.name}</a></li>
{/foreach}
</ul>

{include file="footer.tpl"}
