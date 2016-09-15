{if !$hide && !$hidefooter}
    <div id="return">
        {if !$hidehelplink}<a href="/grase/uam/help">Help</a>&nbsp;|&nbsp;{/if}
        <a href="{$website_link}">{$website_name}</a>&nbsp;|&nbsp;
        <a href="/grase/radmin/usermin">{t}My Account{/t}</a>&nbsp;|&nbsp;
        <a href="{$Support.link}">{t}Support{/t}: {$Support.name}</a>&nbsp;|&nbsp;
        <a href="/grase/radmin/">{t}Admin{/t}</a>

        <div id="copyright">&copy;&nbsp;{$smarty.now|date_format:'%Y'}&nbsp;<a href="http://grasehotspot.org/">Timothy
                White</a></div>
        <div id="generated">
            {php}global $pagestarttime;$mtime = microtime();$mtime = explode(" ",$mtime);$mtime = $mtime[1] + $mtime[0];$endtime = $mtime;$totaltime = round(($endtime - $pagestarttime), 2);echo "Page generated in ".$totaltime." seconds on ";{/php}{$RealHostname}
            using
            {php}function convert($size){$unit=array('b','kb','mb','gb','tb','pb');return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];} echo convert(memory_get_peak_usage(true)) ;{/php}
            mem

            <!-- close 'generated' div -->
        </div>

        <!-- close 'return' div -->
    </div>
{else}
    <!-- Please consider putting a link to http://grasehotspot.org as well as a copyright statement if you are hiding the footer
<div id="copyright">&copy;&nbsp;{$smarty.now|date_format:'%Y'}&nbsp;<a href="http://grasehotspot.org/">Timothy White</a></div>
-->
{/if}
</div>
</body>
</html>
