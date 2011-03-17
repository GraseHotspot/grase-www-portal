{include file="header.tpl" Name="Logged In" activepage="loggedin"}

<div id="page">
<h1>{$Location} Hotspot - Successful Login</h1>


<p> Your login was success full. Please click <a href="nojsstatus" target="_nojsstatus">HERE</a> to open a status window</p>
{if $user_url}<span><a href="{$user_url}">Continue to your site {$user_url}</a></span>{/if}


<div style="clear: left; clear: right">&nbsp;</div>




{include file="footer.tpl"}
