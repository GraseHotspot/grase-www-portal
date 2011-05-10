{include file="header.tpl" Name="Logged In" activepage="loggedin"}

<div id="page">
<h1>{$Location} Hotspot - {t}Successful Login{/t}</h1>


<p>{t}Your login was successful.{/t} {t escape=no}Please click <a href="nojsstatus" target="grasestatus">HERE</a> to open a status window{/t}<br/>{t escape=no}If you don't open a status window, then bookmark the link <a href="http://logout/">http://logout/</a> so you can logout when finished.{/t}</p>
{if $user_url}<span><a href="{$user_url}">{t}Continue to your site{/t} {$user_url|truncate:60}</a></span>{/if}


<div style="clear: left; clear: right">&nbsp;</div>




{include file="footer.tpl"}
