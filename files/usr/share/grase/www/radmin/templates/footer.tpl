</div>
{* Disabled due to too much confusion over the use of these, also need to be moved to the authenticated area (maybe use display_page function to do header, footer, and status bars)
{if $activepage!="error"}
    {include file="statusmonitors.tpl"}
{/if}
*}

<div id="return"><a href="/grase/uam/help">{t}Help Page{/t}</a>&nbsp;|&nbsp;<a href="{$website_link}">{$website_name}</a>&nbsp;|&nbsp;<a href="/grase/radmin/usermin">{t}My Account{/t}</a>&nbsp;|&nbsp;<a href="/grase/radmin/">{t}Admin{/t}</a>
<div id="copyright">&copy;&nbsp;{$smarty.now|date_format:'%Y'}&nbsp;<a href="http://grasehotspot.org/">Timothy White</a></div>

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
{php}echo \Grase\Util::formatBytes(memory_get_peak_usage(true)) ;{/php} mem
</div>
</div>
<div style="clear: both">&nbsp;</div>
</div>

<div id="logo">&nbsp;</div>

{if $DEMOSITE}
{literal}
<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.purewhite.id.au/" : "http://piwik.purewhite.id.au/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 9);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://piwik.purewhite.id.au/piwik.php?idsite=9" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Tag -->
{/literal}
{/if}

</body>
</html>
