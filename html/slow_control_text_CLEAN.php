<?php
// slow_control_text.php
// Part of the CLEAN slow control.  
// James Nikkel, Yale University, 2006.
// james.nikkel@yale.edu
//
session_start();
$req_priv = "basic";
include("db_login.php");
include("slow_control_page_setup.php");

///  Get all the sensor information from the database so that we can 
///  define what we want to plot.
include("aux/get_sensor_info.php"); 

///  The idea here is to create a list of unique sensor types from all the available sensors.
///  This allows us to just view one type at a time on the screen.
include("aux/choose_types.php");    // sets session variable $choose_type from the check boxes.

///  Read the last values from the database and save them to an array $sensor_values
///  indexed by $sensor_name
if (!(empty($_POST['num_val_avgs'])))
{
    $_SESSION['num_val_avgs'] = $_POST['num_val_avgs'];
}
include("aux/get_sensor_vals.php");
mysql_close($connection);

////   This next section generate the HTML with the plot names in a table.

$cur_time = time();
echo ('<TABLE border="1" cellpadding="2" width=100%>');
$i=1;
echo ('<TR>');
foreach ($_SESSION['choose_type'] as $unique_sensor_type)
{
    foreach ($sensor_names as $sensor_name)
    {
	if ((strcmp($sensor_types[$sensor_name], $unique_sensor_type) == 0) && ($sensor_settable[$sensor_name] == 0)) 
	{                                                                      // don't show settable or hidden sensors in this table
	    echo ('<TH>');
	    if ($sensor_al_trip[$sensor_name])
		echo ('<font color="red">');
	    echo ($sensor_descs[$sensor_name]." (".$sensor_name.")");
	    echo ('<br>');
	    echo ('<font size="+4">');
	    if  (strncmp($sensor_units[$sensor_name], "discrete", 8) == 0)    // discrete value sensors
	    {	
		echo (get_disc_units_val($sensor_values[$sensor_name], $sensor_discrete_vals[$sensor_name]));
		echo ('</font>');
		echo ('<br>');	
	    }
	    else
	    {
		echo (format_num2($sensor_values[$sensor_name], $sensor_num_format[$sensor_name]));
		echo ('</font>');
		echo ('<br>');
		echo ('('.$sensor_units[$sensor_name].')');
	    }
	    echo ('</TH>');
	    if ($i % 3 ==0)
	    {
		echo ('</TR>');
		echo ('<TR>');
	    }
	    $i++;
	}
	if ((strcmp($sensor_types[$sensor_name], $unique_sensor_type) == 0) && ($sensor_settable[$sensor_name] == 0) && ($sensor_show_rate[$sensor_name] == 1)) //rates
	{                                                                      // don't show settable or hidden sensors in this table
	    echo ('<TH>');
	    if ($sensor_al_trip[$sensor_name])
		echo ('<font color="red">');
	    echo ("Rate of ".$sensor_descs[$sensor_name]." (".$sensor_name.")");
	    echo ('<br>');
	    echo ('<font size="+4">');
	    echo (format_num2($sensor_rates[$sensor_name], $sensor_num_format[$sensor_name]));
	    echo ('</font>');
	    echo ('<br>');
	    echo ('('.$sensor_units[$sensor_name].'/s)');
	    echo ('</TH>');
	    if ($i % 3 ==0)
	    {
		echo ('</TR>');
		echo ('<TR>');
	    }
	    $i++;
	}
    }
}
echo ('</TR>');
echo ('</TABLE>');


$num_val_avgs_array = array(
    "1" => 1,
    "3" => 3,
    "5" => 5,
    "10" => 10,
    "20" => 20
    );

echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<TABLE border="0" cellpadding="1" cellspacing="2">');
echo ('<TD align=left>');
echo ('Number of values to average: <select name="num_val_avgs"> ');
foreach ($num_val_avgs_array as $st_s => $st_v)
{
    echo('<option ');
    if ($st_v == $_SESSION['num_val_avgs'])
    {
	echo ('selected="selected"');
    }
    echo(' value="'.$st_v.'" >'.$st_s.'</option> ');
}
echo ('</select>');
echo ('</TD>');
echo ('<TD align=center>');
echo ('<input type="image" src="pixmaps/reload.png" value="Change" alt="Refresh" title="Refresh page with new number of values">');
echo ('</TD>');
echo ('</FORM>');

echo(' </body>');
echo ('</HTML>');
?>
