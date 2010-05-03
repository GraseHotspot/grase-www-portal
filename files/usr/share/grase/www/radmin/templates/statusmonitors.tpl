<div id="StatusMonitors">

<!-- Progress Bars -->
	<!-- jsProgressBarHandler prerequisites : prototype.js -->
	<script type="text/javascript" src="js/prototype/prototype.js"></script>
	<!-- jsProgressBarHandler core -->
	<script type="text/javascript" src="js/bramus/jsProgressBarHandler.js"></script>
<!-- /Progress bars -->

<div id="MonitorBars">
{if $SoldOctetsPercent}<div class="DataBarsNew">
	Sold <br/>{$SoldOctets|bytes} of {$TotalSellableData|bytes}<br/>
<span class="progressBar" id="SoldData">{$SoldOctetsPercent}%</span>
</div>
{/if}
{if $DataUsagePercent}<div class="DataBarsNew">
	This Month Used <br/>{$DataUsageOctets|bytes} of {$TotalUseableData|bytes}<br/>
<span class="progressBar" id="UsedData">{$DataUsagePercent}%</span>
</div>{/if}
{if $LastM_DataUsagePercent}<div class="DataBarsNew">
	Last Month Used <br/>{$LastM_DataUsageOctets|bytes} of {$TotalUseableData|bytes}<br/>
<span class="progressBar" id="LastM_UsedData">{$LastM_DataUsagePercent}%</span>
</div>{/if}
</div>
</div>
<div style="clear: both">&nbsp;</div>

