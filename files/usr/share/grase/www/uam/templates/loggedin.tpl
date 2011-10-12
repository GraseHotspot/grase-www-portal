{include file="header.tpl" Name="Logged In" activepage="loggedin"}

<div id="page">
{if !$hideheader}
<h1>{$Location} Hotspot - {t}Successful Login{/t}</h1>
{/if}

{if $tpl_loggedinnojshtml}{$tpl_loggedinnojshtml}{else}
<p>Your login was successful. Please click <a href="nojsstatus" target="grasestatus">HERE</a> to open a status window<br/>If you don't open a status window, then bookmark the link <a href="http://1.0.0.0/">http://1.0.0.0/</a> so you can logout when finished.</p>
{/if}


{if $user_url}<span><a href="{$user_url}">{t}Continue to your site{/t} {$user_url|truncate:60}</a></span>{/if}


<div style="clear: left; clear: right">&nbsp;</div>




{include file="footer.tpl"}
