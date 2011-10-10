{include file="header.tpl" Name="" activepage="mini"}

<p id="location_name" >{$Location}</p>

<div id="noLocation" style="display:none;">
<p style="padding-top: 100px;"><strong>You are not at a hotspot.</strong>
Please don't try and access this page directly.
</p>
</div>

{literal}<script type="text/javascript"><!--//--><![CDATA[//><!--
    var miniportal = true;

//--><!]]></script>{/literal}

<script id='chillijs' src='http://{$serverip}/grase/uam/js.php?js=chilli.js'></script>
<!--{if $user_url}<span id='origurl'><a href="{$user_url}">Original URL {$user_url}</a></span>{/if}-->




{include file="footer.tpl" hide="true"}
