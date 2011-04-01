{include file="header.tpl" Name="Reports" activepage="reports"}
<p>Reports are current a work in progress. All feedback about the kind of data you wish to see and how it is represented should be directed to <a href="http://hotspot.purewhite.id.au">Tim</a></p>
<div id='reportspage'>
<script type="text/javascript" src="js/json/json2.js"></script>
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript">
{literal}
swfobject.embedSWF("open-flash-chart.swf", "current_month_users_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=current_month_users_usage", "id":"current_month_users_usage"});

swfobject.embedSWF("open-flash-chart.swf", "current_month_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=current_month_usage", "id":"current_month_usage"});

swfobject.embedSWF("open-flash-chart.swf", "previous_months_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=previous_months_usage", "id":"previous_months_usage"});

swfobject.embedSWF("open-flash-chart.swf", "months_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=months_usage", "id":"months_usage"});

swfobject.embedSWF("open-flash-chart.swf", "daily_users", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=daily_users", "id":"daily_users"});

swfobject.embedSWF("open-flash-chart.swf", "daily_sessions", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=daily_sessions", "id":"daily_sessions"});

{/literal}
</script>


<div id="current_month_users_usage"></div>
<div id="current_month_usage"></div>
<div id="previous_months_usage"></div>
<div id="months_usage"></div>
<div id="daily_users"></div>
<div id="daily_sessions"></div>

</div>
<p>&nbsp;</p>

{include file="footer.tpl"}
