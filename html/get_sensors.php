<?php
session_start();
  /* This queries the database for the memory sensor names and returns them in an array */
include ("db_login.php");

$query = "SELECT * FROM sc_sensors WHERE user2='mem'";
$result=mysql_query($query);
//$row=mysql_fetch_row($result);

if (!$result)
  {
    die ("Could not query the database <br>" . mysql_error());
  }

$mem_sensor_names = array();
$len=0;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $mem_sensor_names[] = $row['name'];
    $len++;
  }

$_SESSION['mem_sensor_names']=$mem_sensor_names;

mysql_free_result($result);

/* Also get NET sensor names and store in array */
$query="SELECT * FROM sc_sensors WHERE type='3' AND units='BYTES/second'";
$result=mysql_query($query);

if (!$result)
  {
    die ("Could not query the database <br>" . mysql_error());
  }

$net_sensor_names=array();
$len=0;

while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $net_sensor_names[] = $row['name'];
    $len++;
  }

mysql_free_result($result);

$_SESSION['net_sensor_names']=$net_sensor_names;


?>    