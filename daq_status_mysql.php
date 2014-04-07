<?php

session_start();
include("db_login.php");
include("flowchart.php");
include("get_sensors.php");
include("get_times.php");

//Define instantaneous as last five minutes
$time_interval=1000;

$recent_time = $_SESSION['mem_t_max'] - $time_interval;

$query = "SELECT * FROM sc_sens_FEPC1_MM WHERE `time` > ".$recent_time;

$result = mysql_query($query);

if(!$result)
  {
    die("Could not query the database <br>". mysql_error());
  }

$row=mysql_fetch_row($result);
$num_fields=mysql_num_fields($result);
$return=array();

$j=0;
while($row=mysql_fetch_array($result))
  {
    for($i=0;$i<($num_fields-1);$i++)
      {
	$return[$i][$j] = $row[$i];
      }
    $j++;
  }

flow_chart("fepc1.png",$return,$num_fields,$j);
?>

