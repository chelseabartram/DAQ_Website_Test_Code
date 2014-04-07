<?php
session_start();
foreach($_SESSION['mem_sensor_names'] as $name)
  {
    echo $name;
    echo "<br>";
  }
?>