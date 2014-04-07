<?php
session_start();

include("db_login.php");

$min_t = array();
$max_t = array();

foreach($_SESSION['mem_sensor_names'] as $mem_sensor)
  {

    $query = "SELECT MIN(`time`) AS `time` FROM sc_sens_".$mem_sensor;
    $result = mysql_query($query);

    if(!$result)
      {
	die("Could not query the database <br>" . mysql_error());
      }

    while ($res = mysql_fetch_row($result))
      {
	if (!is_null($res[0]))
	  $min_t[] = $res[0];
      }

    $query = "SELECT MAX(`time`) AS `time` FROM sc_sens_".$mem_sensor;
    $result = mysql_query($query);
    if (!$result)
      {
	die ("Could not query the database <br>" . mysql_error());
      }

    while ($res = mysql_fetch_row($result))
      {
	if (!is_null($res[0]))
	  $max_t[] = $res[0];
      }
  }


$_SESSION['mem_t_min'] = min($min_t);
$_SESSION['mem_t_max'] = max($max_t);

?>

