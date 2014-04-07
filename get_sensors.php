<?php
session_start();
  /* This queries the database for the memory sensor names and returns them in an array */
include ("db_login.php");

$query = "SELECT * FROM sc_sensors WHERE user2='mem'";
$result=mysql_query($query);
$row=mysql_fetch_row($result);

if (!$result)
  {
    die ("Could not query the database <br>" . mysql_error());
  }

$mem_sensor_names = array();

while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $mem_sensor_names[] = $row['name'];
  }


$_SESSION['mem_sensor_names']=$mem_sensor_names;

?>    