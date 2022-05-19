
 <script src="jquery.csv.min.js"></script>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>

<script type="text/javascript"> // load the visualisation API
  google.load('visualization', '1', { packages: ['corechart', 'controls'] });
</script>

<script type="text/javascript">
function drawVisualization() {
   $.get("google.csv", function(csvString) {
      // transform the CSV string into a 2-dimensional array
      var arrayData = $.csv.toArrays(csvString, {onParseValue: $.csv.hooks.castToScalar});

      // this new DataTable object holds all the data
      var data = new google.visualization.arrayToDataTable(arrayData);
      // CAPACITY - En-route ATFM delay - YY - CHART
      var crt_ertdlyYY = new google.visualization.ChartWrapper({
         chartType: 'LineChart',
         containerId: 'crt_ertdlyYY',
         dataTable: data,
         options:{
            width: 920, height: 200,
            title: 'あなたの今月の勤務時間',
            titleTextStyle : {color: 'grey', fontSize: 11},
         }
      });
      crt_ertdlyYY.draw();
   });
}
google.setOnLoadCallback(drawVisualization)
</script>