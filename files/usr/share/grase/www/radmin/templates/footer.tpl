</div>

{if $activepage!="error"}
    {include file="statusmonitors.tpl"}
{/if}

<div id="return"><a href="/grase/uam/help">{t}Help Page{/t}</a>&nbsp;|&nbsp;<a href="{$website_link}">{$website_name}</a>&nbsp;|&nbsp;<a href="/grase/radmin/usermin">{t}My Account{/t}</a>&nbsp;|&nbsp;<a href="/grase/radmin/">{t}Admin{/t}</a>
<div id="copyright">&copy;&nbsp;{$smarty.now|date_format:'%Y'}&nbsp;<a href="http://hotspot.purewhite.id.au/">Timothy White</a></div>

<div id="generated">
{php}
   global $pagestarttime;
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = round(($endtime - $pagestarttime), 2);
   echo "Page generated in ".$totaltime." seconds on ";    
{/php}{$RealHostname} using
{php}echo Formatting::formatBytes(memory_get_peak_usage(true)) ;{/php} mem
</div>
</div>
<div style="clear: both">&nbsp;</div>
</div>

<div id="logo">&nbsp;</div>
</body>
</html>
