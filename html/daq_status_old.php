<?php

session_start();
include("db_login.php");
include("flowchart.php");
include("net_flowchart.php");
include("get_sensors.php");
include("get_times.php");

//Define instantaneous as last five minutes
$time_interval=1000;

$recent_time = $_SESSION['mem_t_max'] - $time_interval;

foreach($_SESSION['mem_sensor_names'] as $mem_sensor_name)
  {

    echo $mem_sensor_name;
    $query = "SELECT * FROM sc_sens_".$mem_sensor_name." WHERE `time` > ".$recent_time;

    $result = mysql_query($query);
    
    if(!$result)
      {
	die("Could not query the database <br>". mysql_error());
      }
    
    $row=mysql_fetch_row($result);
    $num_fields=mysql_num_fields($result);
    $return=array();
    $field_names=array();

    for($field_index=0;$field_index<$num_fields;$field_index++)
      {
	$field_names[$field_index]=mysql_field_name($result,$field_index);
      }
    
    $j=0;
    while($row=mysql_fetch_array($result))
      {
	for($i=0;$i<($num_fields-1);$i++)
      {
	$return[$i][$j] = $row[$i];
      }
	$j++;
      }
    
    $plot_name = "memplot_".$mem_sensor_name."_";

    flow_chart($plot_name,$return,$num_fields,$j,$field_names);

    mysql_free_result($result);
  }

//Same but for net sensors
foreach($_SESSION['net_sensor_names'] as $net_sensor_name)
  {
    $query = "SELECT * FROM sc_sens_".$net_sensor_name." WHERE `time` > ".$recent_time;

    $result = mysql_query($query);
    
    if(!$result)
      {
	die("Could not query the database <br>". mysql_error());
      }
    
    $row=mysql_fetch_row($result);
    $num_fields=mysql_num_fields($result);
    $return=array();
    $field_names=array();

    for($field_index=0;$field_index<$num_fields;$field_index++)
      {
	$field_names[$field_index]=mysql_field_name($result,$field_index);
      }
    
    $j=0;
    while($row=mysql_fetch_array($result))
      {
	for($i=0;$i<($num_fields-1);$i++)
      {
	$return[$i][$j] = $row[$i];
      }
	$j++;
      }
    
    $plot_name = "netplot_".$net_sensor_name;

    net_flow_chart($plot_name,$return,$num_fields,$j,$field_names);

    mysql_free_result($result);
  }
?>








