{include file="header.tpl" Name="Reports" activepage="reports"}
<div id='reportspage'>

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




</div>
<p>&nbsp;</p>

{include file="footer.tpl"}
