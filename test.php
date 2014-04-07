<?php

session_start();

$query="SELECT * from sc_sensors";
$result=mysql_query($query);
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
echo $return[2][0];
echo " ";
echo $_SESSION['t_max_p'];

if (!$result)
  {
    echo 'could not run mysql query';
    exit;
  }

?>