<div id="StatusMonitors">

<!-- Progress Bars -->

  <script type="text/javascript">
  $(document).ready(function() {ldelim}
    $("#SoldData").progressbar({ldelim} value: {$SoldOctetsPercent+0} {rdelim});
    $("#UsedData").progressbar({ldelim} value: {$DataUsagePercent+0} {rdelim});
    $("#LastM_UsedData").progressbar({ldelim} value: {$LastM_DataUsagePercent+0} {rdelim});            
  {rdelim});
  </script>	
<!-- /Progress bars -->

<div id="MonitorBars">
{if $SoldOctetsPercent}<div class="DataBarsNew">
	{t}Sold{/t}<br/>{t 1=$SoldOctets|bytes 2=$TotalSellableData|bytes}%1 of %2{/t}<br/>
<div id="SoldData"></div>
</div>
{/if}
{if $DataUsagePercent}<div class="DataBarsNew">
	{t}This Month Used{/t} <br/>{t 1=$DataUsageOctets|bytes 2=$TotalUseableData|bytes}%1 of %2{/t}<br/>
<div id="UsedData"></div>
</div>{/if}
{if $LastM_DataUsagePercent}<div class="DataBarsNew">
	{t}Last Month Used{/t} <br/>{t 1=$LastM_DataUsageOctets|bytes 2=$TotalUseableData|bytes}%1 of %2{/t}<br/>
<div id="LastM_UsedData"></div>
</div>{/if}
</div>
</div>
<div style="clear: both">&nbsp;</div>

