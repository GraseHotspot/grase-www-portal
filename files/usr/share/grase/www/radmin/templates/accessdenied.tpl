{include file="header.tpl" Name="Access Denied" activepage="accessdenied"}

<h1 class="error">{t}Access Denied{/t}</h1>
<p class="error">{t}You have attempted to access a page outside of your access level. Please return to an area you have access to.{/t}<br/>
<br/>
{$error}
</p>
<!--<p>{$memory_used}</p>-->

{include file="footer.tpl" activepage="accessdenied"}
