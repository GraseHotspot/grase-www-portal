{include file="header.tpl" Name="Error" activepage="error"}

<h1 class="error">{t}Error{/t}</h1>
<p class="error">{t}An Error has occured. Please report the error to the administrator{/t}<br/>
{t}There may be more information available to the administrator in the logs{/t}
<br/>
{$error}
</p>
<!--<p>{$memory_used}</p>-->

{include file="footer.tpl" activepage="error"}
