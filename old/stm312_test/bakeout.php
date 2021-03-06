<?php
/**
 * linear regression function
 * @param $x array x-coords
 * @param $y array y-coords
 * @returns array() m=>slope, b=>intercept
 */
function linear_regression($x, $y) {

  // calculate number points
  $n = count($x);

  // ensure both arrays of points are the same size
  if ($n != count($y)) {

    trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);

  }

  // calculate sums
  $x_sum = array_sum($x);
  $y_sum = array_sum($y);

  $xx_sum = 0;
  $xy_sum = 0;

  for($i = 0; $i < $n; $i++) {

    $xy_sum+=($x[$i]*$y[$i]);
    $xx_sum+=($x[$i]*$x[$i]);

  }

  // calculate slope
  $m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));

  // calculate intercept
  $b = ($y_sum - ($m * $x_sum)) / $n;

  // return result
  return array("m"=>$m, "b"=>$b);

}

$db = mysql_connect("localhost", "cinf_reader", "cinf_reader");  
mysql_select_db("cinfdata",$db);

$query = "SELECT temperature FROM temperature_stm312 order by time desc limit 1";
$result  = mysql_query($query,$db);
$row = mysql_fetch_array($result);
$temperature = $row[0];

$query = "SELECT time FROM temperature_stm312 order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$temperature_time = $row[0];

$query = "SELECT pressure FROM pressure_stm312 order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$pressure = $row[0];

$query = "SELECT time FROM pressure_stm312 order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$pressure_time = $row[0];

$query = "SELECT UNIX_TIMESTAMP(time), temperature FROM temperature_stm312 order by time desc limit 5";
$result  = mysql_query($query,$db);
while ($row = mysql_fetch_array($result)){
        $ydata[] = $row[1];
        $xdata[] = $row[0];
}

$regression_results = linear_regression($xdata, $ydata);
$temperature_slope = $regression_results[m]*60;

?>

<html>
<head>
<title>Bakeout status page STM 312</title>
</head>
<body>
 <table border="0">
    <tr>
      <td><img src="oven.jpg"></td>
      <td><h1>Overview of the bakeout status STM 312</h1></td>
      <td><img src="setup.jpg"></td>
    </tr>
  </table>
  <h2>Current pressure: <?php echo($pressure)?> torr, at <?php echo($pressure_time)?></h2>
  <h2>Current temperature: <?php echo(round($temperature,2))?>&deg;C, at <?php echo($temperature_time)?></h2>
  <h2>Temperature slope: <?php echo(round($temperature_slope,2))?>&deg;C/min, last 5 points</h2>

<img src="plot.php?type=pressure_bakeout&small_plot=1&xsize=400&ysize=300&manualscale=1&ymax=1E-5&ymin=1E-10"><img src="plot.php?type=temperature_bakeout&small_plot=1&xsize=400&ysize=300&manualscale=1&ymax=200&ymin=0"><br>
<h1>Full size graphs</h1>
<img src="plot.php?type=pressure_bakeout&manualscale=1&ymax=1E-5&ymin=1E-10"><br><img src="plot.php?type=temperature_bakeout&manualscale=1&ymax=200&ymin=0">
</body>
</html>





