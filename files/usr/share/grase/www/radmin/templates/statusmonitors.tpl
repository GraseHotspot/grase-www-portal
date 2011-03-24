<div id="StatusMonitors">

<!-- Progress Bars -->

  <script>
  $(document).ready(function() {ldelim}
    $("#SoldData").progressbar({ldelim} value: {$SoldOctetsPercent+0} {rdelim});
    $("#UsedData").progressbar({ldelim} value: {$DataUsagePercent+0} {rdelim});
    $("#LastM_UsedData").progressbar({ldelim} value: {$LastM_DataUsagePercent+0} {rdelim});            
  {rdelim});
  </script>	
<!-- /Progress bars -->

<div id="MonitorBars">
{if $SoldOctetsPercent}<div class="DataBarsNew">
	Sold <br/>{$SoldOctets|bytes} of {$TotalSellableData|bytes}<br/>
<div id="SoldData"></div>
</div>
{/if}
{if $DataUsagePercent}<div class="DataBarsNew">
	This Month Used <br/>{$DataUsageOctets|bytes} of {$TotalUseableData|bytes}<br/>
<div id="UsedData"></div>
</div>{/if}
{if $LastM_DataUsagePercent}<div class="DataBarsNew">
	Last Month Used <br/>{$LastM_DataUsageOctets|bytes} of {$TotalUseableData|bytes}<br/>
<div id="LastM_UsedData"></div>
</div>{/if}
</div>
</div>
<div style="clear: both">&nbsp;</div>

