{if !$hide}
<div id="return"><a href="/radmin/usermin">My Account</a>&nbsp;|&nbsp;<a href="/">Welcome Page</a>&nbsp;|&nbsp;<a href="{$website_link}">{$website_name}</a>
<div id="copyright">&copy;&nbsp;{$smarty.now|date_format:'%Y'}&nbsp;<a href="http://www.purewhite.id.au/">Timothy White</a></div>
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
{php}
function convert($size)
 {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 }
   echo convert(memory_get_peak_usage(true)) ; // 36640
{/php} mem
</div>
</div>
{else}
<div id="copyright">&copy;&nbsp;{$smarty.now|date_format:'%Y'}&nbsp;<a href="http://www.purewhite.id.au/">Timothy White</a></div>
{/if}
</body>
</html>
