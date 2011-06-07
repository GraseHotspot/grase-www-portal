{include file="header.tpl" Name="Reports" activepage="reports"}
<p>Reports are current a work in progress. All feedback about the kind of data you wish to see and how it is represented should be directed to <a href="http://hotspot.purewhite.id.au">Tim</a></p>
<div id='reportspage'>
{* <script type="text/javascript" src="js/json/json2.js"></script>
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript"> *}
{* literal}
swfobject.embedSWF("open-flash-chart.swf", "current_month_users_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=current_month_users_usage", "id":"current_month_users_usage"});

swfobject.embedSWF("open-flash-chart.swf", "current_month_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=current_month_usage", "id":"current_month_usage"});

swfobject.embedSWF("open-flash-chart.swf", "previous_months_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=previous_months_usage", "id":"previous_months_usage"});

swfobject.embedSWF("open-flash-chart.swf", "months_usage", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=months_usage", "id":"months_usage"});

swfobject.embedSWF("open-flash-chart.swf", "daily_users", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=daily_users", "id":"daily_users"});

swfobject.embedSWF("open-flash-chart.swf", "daily_sessions", "500", "200", "9.0.0", "expressInstall.swf", {"data-file":"reports?chart=daily_sessions", "id":"daily_sessions"});

{/literal }
</script> *}
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="/grase/js/jqplot/dist/excanvas.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="/grase/js/jqplot/dist/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>

<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.cursor.min.js"></script>

<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="/grase/js/jqplot/dist/plugins/jqplot.barRenderer.min.js"></script>

<link rel="stylesheet" type="text/css" href="/grase/js/jqplot/dist/jquery.jqplot.css" />


<div id="thismonthdata" style="height:400px; width:100%">&nbsp;</div>

<script>
var ticks = {$thismonthticks};
$.jqplot('thismonthdata',  [{$thismonthdowndata},{$thismonthupdata}], 
{literal}
{
      title:'Daily Usage',
      stackSeries: true,
      seriesDefaults:{
        renderer:$.jqplot.BarRenderer,
        pointLabels: {
            hideZeros: true,
            show: true,
            formatString: '%.0f Mb'
	    } 
      },
      gridPadding:{right:35},
      series:[
       {pointLabels:{location:'s'}},
       {pointLabels:{location:'s'}}
      ],
      axesDefaults: {

      },    
      axes:{
        xaxis:{
            renderer:$.jqplot.CategoryAxisRenderer,
            ticks: ticks,
            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
            tickOptions: {
              angle: -30
            }                    
        },
        yaxis: {
            min: 0,
            label: "Mb's used"
        }
      },
      legend: {
          labels: ['Downloads', 'Uploads'],
          show: true,
          placement: 'outsideGrid'
      }     

  });
{/literal}  
</script>

<div id="thismonthusersdata" style="height:400px; width:100%">&nbsp;</div>

<script>
var ticks = {$thismonthuserslabels};
$.jqplot('thismonthusersdata',  [{$thismonthusersdata},{$thismonthusersquota}], 
{literal}
{
      title:'Users Usage',
      stackSeries: false,
      seriesDefaults:{
        renderer:$.jqplot.BarRenderer,
        pointLabels: {
            hideZeros: true,
            show: true,
            formatString: '%.0f'
	    } 
      },
      gridPadding:{right:35},
      series:[
       {pointLabels:{location:'s'}},
       {pointLabels:{location:'s'}}       
      ],
      axesDefaults: {

      },    
      axes:{
        xaxis:{
            renderer:$.jqplot.CategoryAxisRenderer,
            ticks: ticks,
            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
            tickOptions: {
              angle: -30
            }                    
        },
        yaxis: {
            min: 0,
            label: "Mb's used"
        }
      },
      legend: {
          labels: ['Used', 'Total Quota'],
          show: true,
          placement: 'outsideGrid'
      }     

  });
{/literal}  
</script>

<div id="previousmonthsdata" style="height:400px; width:100%">&nbsp;</div>

<script>
var ticks = {$previousmonthsticks};
$.jqplot('previousmonthsdata',  [{$previousmonthsdata}], 
{literal}
{
      title:'Months Usage',
      stackSeries: true,
      seriesDefaults:{
        renderer:$.jqplot.BarRenderer
      },
      gridPadding:{right:35},
      axesDefaults: {

      },    
      axes:{
        xaxis:{
            renderer:$.jqplot.CategoryAxisRenderer,
            ticks: ticks,
            tickRenderer: $.jqplot.CanvasAxisTickRenderer,
            tickOptions: {
              angle: -30
            }                    
        },
        yaxis: {
            min: 0,
            label: "Mb's used"
        }
      },
      legend: {
          labels: ['Usage'],
          show: true,
          placement: 'outsideGrid'
      },
      highlighter: {
        show: true,
        sizeAdjust: 7.5
      },
      cursor: {
        show: false
      }       
    

  });
{/literal}  
</script>


<div id="thismonthgrouppie" style="height:400px; width:100%"></div>

<script>
var thismonthgrouppie = {$thismonthgroupdata};
{literal}
$.jqplot('thismonthgrouppie',  [thismonthgrouppie], {
     title:'Group Usage',
        grid: {
            drawBorder: false, 
            drawGridlines: false,
            background: '#ffffff',
            shadow:false
        },
        axesDefaults: {
            
        },
        seriesDefaults:{
            renderer:$.jqplot.PieRenderer,
            rendererOptions: {
                showDataLabels: true
            }
        },
        legend: {
            show: true,
            rendererOptions: {
                numberRows: 1
            },
            location: 's'
        }
    }); 
{/literal}
</script>

{*
<div id="current_month_users_usage"></div>
<div id="current_month_usage"></div>
<div id="previous_months_usage"></div>
<div id="months_usage"></div>
<div id="daily_users"></div>
<div id="daily_sessions"></div>
*}

</div>
<p>&nbsp;</p>

{include file="footer.tpl"}
