{include file="header.tpl" Name="Reports" activepage="reports"}
<p>Reports are current a work in progress. All feedback about the kind of data you wish to see and how it is represented should be directed to <a href="http://grasehotspot.org">Tim</a></p>
<div id='reportspage'>

<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="/grase/js/jqplot/dist/excanvas.js"></script><![endif]-->
<!--<script language="javascript" type="text/javascript" src="/grase/js/jqplot/dist/jquery.jqplot.min.js"></script>-->
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

    {literal}
<script type="text/javascript"
  src='https://www.google.com/jsapi?autoload={"modules":[{"name":"visualization","version":"1","packages":["controls"]}]}'>
</script>


    <script type="text/javascript">


      google.setOnLoadCallback(drawCharts);
      function drawCharts()
      {
        drawCurrentMonthChart();
        drawAllMonthsChart();
      }
      
      
      function drawCurrentMonthChart() {
      {/literal}
        var data = google.visualization.arrayToDataTable({$thismonthupdownarray});
        {literal}
        
        var dashboard = new google.visualization.Dashboard(
            document.getElementById('thismonthuserdatadashboard'));
            
        var dateSlider = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'thismonthuserdataslider',
            'options': {
                'filterColumnLabel': 'Downloads'
            }
        });

        var chart = new google.visualization.ChartWrapper({
            'chartType': 'ColumnChart',
            'containerId': 'thismonthuserdatachart',
            'options':{
                  title: 'Daily Usage',
                  isStacked: true,
                  animation: {duration: 500},
                  hAxis: {title: 'Day', titleTextStyle: {color: 'red'}}
                }
            });
            
        dashboard.bind(dateSlider, chart);
        dashboard.draw(data);
      }
      

      function drawAllMonthsChart() {
      {/literal}
        var data = google.visualization.arrayToDataTable({$userusagebymontharray});
        {literal}
        
        var dashboard = new google.visualization.Dashboard(
            document.getElementById('allmonthsdatadashboard'));
            
        var dataSlider = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'allmonthsdataslider',
            'options': {
                'filterColumnLabel': 'Total Data'
            }
        });
        
        var timeSlider = new google.visualization.ControlWrapper({
            'controlType': 'NumberRangeFilter',
            'containerId': 'allmonthsdatatimeslider',
            'options': {
                'filterColumnLabel': 'Total Time'
            }
        });
        
        
        var monthFilter = new google.visualization.ControlWrapper({
            'controlType': 'CategoryFilter',
            'containerId': 'allmonthsdatamonthfilter',
            'options': {
                'filterColumnLabel': 'Month'
            }
        });
                
        
        var table = new google.visualization.ChartWrapper({
            'chartType': 'Table',
            'containerId': 'allmonthsdatatable',
            'options': {
              page: 'enable'
            }
          });        

        var chart = new google.visualization.ChartWrapper({
            'chartType': 'ColumnChart',
            'containerId': 'allmonthsdatachart',
            'view': {'columns': [0, 2, 3]},
            
            'options':{
                vAxes:[
                    {title:'Data (Mb\'s)', minValue: 0, viewWindowMode: "explicit", viewWindow:{ min: 0 }}, // Nothing specified for axis 0
                    {title:'Time (Minutes)', minValue: 0, viewWindowMode: "explicit", viewWindow:{ min: 0 }} // Axis 1
                    ],
                  title: 'Monthly Usage',
                  animation: {duration: 500},
                  series:{
                    0:{targetAxisIndex:0},
                    1:{targetAxisIndex:1}
                    
                  },
                  hAxis: {title: 'User'}
                }
            });
        dashboard.bind(monthFilter, [dataSlider, timeSlider])
        dashboard.bind([dataSlider, timeSlider, monthFilter], [chart, table]);
        dashboard.draw(data);
      }
      {/literal}      
    </script>


<script>
//var ticks = 
//var seriesdata
{literal}
function bar_graph(divid, stackSeries, seriesdata, title, legendlabels, yaxislabel)
{
    $.jqplot(divid, seriesdata, 

    {
          title: title,
          stackSeries: stackSeries,
          seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            pointLabels: {
                hideZeros: true,
                show: true,
                formatString: '%.0f',
                location:'s'
	        } 
          },
          gridPadding:{right:35},
          axesDefaults: {

          },    
          axes:{
            xaxis:{
                renderer:$.jqplot.CategoryAxisRenderer,
                sortMergedLabels: true,
                //ticks: ticks,
                tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                tickOptions: {
                  angle: -30
                
                }                    
                
            },
            yaxis: {
                min: 0,
                label: yaxislabel
            }
          },
          legend: {
              labels: legendlabels,
              show: true,
              placement: 'outsideGrid'
          },
          highlighter: {
            show: true,
            sizeAdjust: 7.5,
            tooltipAxes: 'y'
          },
          cursor: {
            show: false
          }     

      });
};

function datebar_graph(divid, stackSeries, seriesdata, title, legendlabels, yaxislabel)
{
    $.jqplot(divid, seriesdata, 

    {
          title: title,
          stackSeries: stackSeries,
          seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {
                barWidth: 20,
                barMargin: 20,
            },
            pointLabels: {
                hideZeros: true,
                show: true,
                formatString: '%.0f',
                location:'s'
	        },
	        
          },
          gridPadding:{right:35},
          axesDefaults: {

          },    
          axes:{
            xaxis:{
                //renderer:$.jqplot.CategoryAxisRenderer,
                //ticks: ticks,
                //tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                //tickOptions: {
                //  angle: -30
                renderer:$.jqplot.DateAxisRenderer,
                //min: seriesdata[0][0][0],
                //max: seriesdata[0][seriesdata[0].length-1][0]
                //}                    
            },
            yaxis: {
                min: 0,
                label: yaxislabel
            }
          },
          legend: {
              labels: legendlabels,
              show: true,
              placement: 'outsideGrid'
          }     

      });
};
{/literal}  
</script>

<form>
<!--<div id="thismonthdata" style="height:400px; width:100%">&nbsp;</div>-->

<script>
datebar_graph('thismonthdata', true, {$thismonthseries}, 'Daily Usage', ['Downloads', 'Uploads'], "Mb's used");

</script>


<div id="thismonthuserdatadashboard">
    <div id="thismonthuserdatachart" style="height:400px; width:100%">&nbsp;</div>
    <div id="thismonthuserdatatable">&nbsp;</div>
    <div id="thismonthuserdataslider">&nbsp;</div>
</div>

<div id="allmonthsdatadashboard">

    <div id="allmonthsdatachart" style="height:400px; width:100%">&nbsp;</div>
    <div id="allmonthsdatamonthfilter">&nbsp;</div>
    <div id="allmonthsdataslider" style="width:100%">&nbsp;</div>
    <div id="allmonthsdatatimeslider">&nbsp;</div>    
    <div id="allmonthsdatatable">&nbsp;</div>    
</div>



<!--<div id="thismonthusersdata" >

<div id="thismonthusersdatagraph" style="height:400px; width:100%">&nbsp;</div>
<div id="thismonthuserstimegraph" style="height:400px; width:100%">&nbsp;</div>
{html_options name="UsersUsageMonth" options=$monthsavailableaccounting selected=$usersusagemonth}
<input type="submit" value="Change Month"/>
</div>-->
<script>
bar_graph('thismonthusersdatagraph', false, {$userdatausagemonthseries}, 'Users Data Usage For {$usersusageprettymonth}', ['Data Used'], "Mb's");
</script>

<script>
bar_graph('thismonthuserstimegraph', false, {$usertimeusagemonthseries}, 'Users Time Usage For {$usersusageprettymonth}', ['Time Used'], "Minutes");
</script>

<div id="previousmonthsdata" style="height:400px; width:100%">&nbsp;</div>

<script>
bar_graph('previousmonthsdata', false, {$previousmonthsseries}, 'Months Usage', ['Usage'], "Mb's used");

</script>


<!--<div id="thismonthgrouppie" style="height:400px; width:100%"></div>-->

</form>

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

</div>
<p>&nbsp;</p>

{include file="footer.tpl"}
