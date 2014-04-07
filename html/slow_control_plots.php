<?php
  // slow_control_display.php
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


/// Load the plotting libraries
include("aux/make_plot.php"); //includes barplot function now


///  Define max and min times for display my finding the very first and very
///  last entry in the database.  These are stored as session variables
///  t_min and t_max.  We then initialize the plotting limits, t_min_p and t_max_p
include("aux/get_time_max_min.php"); 
if ((empty($_SESSION['t_min_p'])) || (empty($_SESSION['t_max_p'])))
  {
    $_SESSION['t_min_p'] = $_SESSION['t_max'] - (24 * 3600);  // plot only last 24 hours of data
    $_SESSION['t_max_p'] = time();  // set to now
  }

///   This session variable array sets the log scaling behavior for _each_ plot.  That is why
///   it is defined here after we get the sensor info, but before we choose the type.
///   It is an associative array based on the sensor names where the value is
///   0 for no log
///   1 for log on x - axis
///   2 for log on y - axis
///   3 for log on both x and y axis 
///   The post variables just need to be before we generate the plots, but are here to 
///   group them with the other loggy stuff.
if (empty($_SESSION['s_logxy']))
  $_SESSION['s_logxy'] = array_combine($sensor_names, make_new_zero_array(count($sensor_names)));

$need_roller = 1;
if (empty($_SESSION['s_roll_up']))
  $_SESSION['s_roll_up'] = array_combine($sensor_names, make_new_zero_array(count($sensor_names)));

if (empty($_SESSION['s_rerange']))
  $_SESSION['s_rerange'] = array_combine($sensor_names, make_new_zero_array(count($sensor_names)));

///  The idea here is to create a list of unique sensor types from all the available sensors.
///  This allows us to just view one type at a time on the screen.
include("aux/choose_types.php");    // sets session variable $choose_type from the check boxes.

if (!(empty($_POST['go_msg_time'])))
  {
    if ($_SESSION['go_msg_time'] == $_POST['go_msg_time'])
      unset($_SESSION['go_msg_time']);
    
    else
      {
	$_SESSION['t_min_p'] = $_POST['go_msg_time'] - 2*3600;
	$_SESSION['t_max_p'] = $_POST['go_msg_time'] + 2*3600;
	$_SESSION['t_max_now'] = -1;
	$_SESSION['go_msg_time'] = $_POST['go_msg_time'];
      }
  }

if (!empty($_POST['plot_view_x_size']))
  if (((int)$_POST['plot_view_x_size'] > 120) AND ((int)$_POST['plot_view_x_size'] < 2000))
    $_SESSION['plot_view_x_size'] = (int)$_POST['plot_view_x_size'];

if (!empty($_POST['plot_view_y_size']))
  if (((int)$_POST['plot_view_y_size'] > 120) AND ((int)$_POST['plot_view_y_size'] < 2000)) 
    $_SESSION['plot_view_y_size'] = (int)$_POST['plot_view_y_size'];


if (empty($_SESSION['plot_view_x_size']))
  {
    $_SESSION['plot_view_x_size'] = 800;
    $_SESSION['plot_view_y_size'] = 400;
  }


///  Now make a form where we can choose the time window from which to plot the data.
///  This creates/sets session variables, t_min_p and t_max_p used below. 
include("aux/choose_times.php");

///   Here we get the actual data from the database and make plots which are kept in a 
///   cache directory.  After this loop is another that actually makes an html table with
///   the plots in it.
$plot_names = array();
$cut_sensor_names = array();

$temp_types = $sensor_types;
$my_sensor_names = array();
foreach ($_SESSION['choose_type'] as $c_index)
  {
    foreach ($sensor_names as $sensor_name)
      if (in_array($c_index, explode(",", $temp_types[$sensor_name])))
	{
	  $my_sensor_names[] = $sensor_name;
	  $temp_types[$sensor_name] = "";
	}
  }
//editing code here
$my_sensor_usrs = array();
foreach ($_SESSION['choose_type'] as $c_index)
  {
    foreach ($sensor_user1 as $sensor_usr1)
      if (in_array($c_index, explode(",", $temp_types[$sensor_usr1])))
	{
	  $my_sensor_usrs[] = $sensor_usr1;
	  $temp_types[$sensor_usr1] = "";
	}
  }

foreach ($my_sensor_names as $sensor_name)
  {
    if ($sensor_settable[$sensor_name] == 0)
      {
	if (!empty($_POST['roll_up']))
	  {
	    if (in_array($sensor_name, $_POST['roll_up']))
	      {
		if ($_SESSION['s_roll_up'][$sensor_name] == 1)
		  $_SESSION['s_roll_up'][$sensor_name] = 0;
		else
		  $_SESSION['s_roll_up'][$sensor_name] = 1;	
	      }
	  }
	    
	if (!empty($_POST['min_all_but']))
	  {
	    if (in_array($sensor_name, $_POST['min_all_but']))
	      $_SESSION['s_roll_up'][$sensor_name] = 0;
	    else
	      $_SESSION['s_roll_up'][$sensor_name] = 1;
	  }

	if ($_SESSION['s_roll_up'][$sensor_name] == 0)
	  {
		
	    if (!empty($_POST['scale-y-button-up']))  
	      {
		if (in_array($sensor_name, $_POST['scale-y-button-up']))
		  $_SESSION['s_rerange'][$sensor_name] += 1;
	      }
	    if (!empty($_POST['scale-y-button-down']))  
	      {
		if (in_array($sensor_name, $_POST['scale-y-button-down']))
		  $_SESSION['s_rerange'][$sensor_name] -= 1;
	      }

	    if (!empty($_POST['log-y-button']))  
	      {
		if (in_array($sensor_name, $_POST['log-y-button']))
		  if ($_SESSION['s_logxy'][$sensor_name] == 2)
		    $_SESSION['s_logxy'][$sensor_name] = 0;
		  else  if ($_SESSION['s_logxy'][$sensor_name] == 1)
		    $_SESSION['s_logxy'][$sensor_name] = 3;
		  else if ($_SESSION['s_logxy'][$sensor_name] == 3)
		    $_SESSION['s_logxy'][$sensor_name] = 1;
		  else
		    $_SESSION['s_logxy'][$sensor_name] = 2;
	      }
		
	    if ($_SESSION['s_logxy'][$sensor_name] == 0)
	      $logxy = "intlin";
	    else if ($_SESSION['s_logxy'][$sensor_name] == 1)
	      $logxy = "loglin";
	    else if ($_SESSION['s_logxy'][$sensor_name] == 2)
	      $logxy = "intlog";
	    else 
	      $logxy = "loglog";
	    //start editing code here
	    $initial_query = "SELECT * FROM sc_sensors WHERE name='".$sensor_name."'";
	    $initial_result=mysql_query($initial_query);
	    $initial_row = mysql_fetch_row($initial_result);
	    if (!$initial_result)
	      {
		echo 'Could not run query: ' . mysql_error();
		exit;
	      }
	    if($initial_row[25]=="default") //end editing of code here
	      {
		if (strcmp($logxy, "intlog") == 0)
		  {
		    $query = "SELECT time, value, rate FROM sc_sens_".$sensor_name." WHERE `time` BETWEEN ".$_SESSION['t_min_p'].
		    " AND ".$_SESSION['t_max_p']." AND `value` > 0 ORDER BY RAND() LIMIT 500";
		  }
		else
		  {
		    $query = "SELECT time, value, rate FROM sc_sens_".$sensor_name." WHERE `time` BETWEEN ".$_SESSION['t_min_p'].
		      " AND ".$_SESSION['t_max_p']." ORDER BY RAND() LIMIT 500";
		  }
	      }
	    else
	    {
		if (strcmp($logxy, "intlog") == 0)
		  $query = "SELECT * FROM (SELECT * FROM sc_sens_".$sensor_name." WHERE `time` BETWEEN ".$_SESSION['t_min_p']." AND ".$_SESSION['t_max_p']." ORDER BY RAND() LIMIT 500) TABLE_ALIAS ORDER BY time";
		else
		  $query = "SELECT * FROM (SELECT * FROM sc_sens_".$sensor_name." WHERE `time` BETWEEN ".$_SESSION['t_min_p']." AND ".$_SESSION['t_max_p']." ORDER BY RAND() LIMIT 500) TABLE_ALIAS ORDER BY time";
	    }
	
	    $result = mysql_query($query);
	    if (!$result)
	      {	
		die ("Could not query the database <br />" . mysql_error());
	      }
		
	    $num_fields=mysql_num_fields($result);
	    $second_field_name= mysql_field_name($result, 1);
	    if($second_field_name!="value")
	      {
		$time = array();
		//defines the array of names to go in the legend
		$name_array = array();
		//defines the array of arrays to be passed to make_barplot function
		$full_field_array = array();

		for( $i=0; $i<$num_fields; $i++ )
		  {
		    $name_array[] =  mysql_field_name($result,$i);
		  }
		
		$row_length=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		  {
		    $time[] = (int)$row['time'];
		    for( $i=0; $i<($num_fields-1); $i++ )
		      {
			//name_array tells mysql what row to get. since it starts at i+1, it skips the time row.
			$full_field_array[$i][] = (double)$row[$name_array[$i+1]];
		      }
		    $row_length++;
		  }
	      	
		if (count($time) > 1)
		  {
		    
		    $plot_name = "jpgraph_cache/plot_".$sensor_name.".png";
		    $plot_title = $sensor_descs[$sensor_name]." (".$sensor_name.")";
		    		    
		    make_memplot($plot_name, $full_field_array, $logxy, $_SESSION['plot_view_x_size'], $_SESSION['plot_view_y_size'], $num_fields, $sensor_name, $time, $row_length, $name_array,  $sensor_user1[$sensor_name], $sensor_user2[$sensor_name] );
		    
		    $plot_names[] = $plot_name;
		    $cut_sensor_names[] = $sensor_name;
		    
		    if ($sensor_show_rate[$sensor_name])
		      {			
			$plot_name = "jpgraph_cache/plot_".$sensor_name."rate.png";
			$plot_title = "Rate of ".$sensor_descs[$sensor_name]." (".$sensor_name.")";
			
			$current_units = range_SI_units($sensor_units[$sensor_name], $_SESSION['s_rerange'][$sensor_name]);
			
			make_memplot($plot_name, $full_field_array, $logxy, $_SESSION['plot_view_x_size'], $_SESSION['plot_view_y_size'], $num_fields, $sensor_name, $time, $row_length, $name_array, $sensor_user1[$sensor_name], $sensor_user2[$sensor_name] );
			
			$plot_names[] = $plot_name;
			$cut_sensor_names[] = $sensor_name;	
		      }
		  }

	      }
	    else
	      {
		$time  = array();
		$value = array();
		$rate  = array();

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		  {	
		    $time[]  = (int)$row['time'];
		    $value[] = (double)$row['value'] * pow(10,(3*$_SESSION['s_rerange'][$sensor_name]));
		    $rate[]  = (double)$row['rate'];
		  }  
		
		if (count($time) > 1)
		  {
		    array_multisort($time, $value, $rate);
		    
		    $plot_name = "jpgraph_cache/plot_".$sensor_name.".png";
		    $plot_title = $sensor_descs[$sensor_name]." (".$sensor_name.")";
		    
		    $current_units = range_SI_units($sensor_units[$sensor_name], $_SESSION['s_rerange'][$sensor_name]);
		    
		    $plot_y_label = "(".$current_units.")";
		    
		    make_plot($plot_name, $time, $value, $logxy, "", $plot_y_label, 
			      $sensor_al_trip[$sensor_name],
			      $_SESSION['plot_view_x_size'], $_SESSION['plot_view_y_size'], NULL, NULL);
		    
		    $plot_names[] = $plot_name;
		    $cut_sensor_names[] = $sensor_name;
		    
		    if ($sensor_show_rate[$sensor_name])
		      {			
			$plot_name = "jpgraph_cache/plot_".$sensor_name."rate.png";
			$plot_title = "Rate of ".$sensor_descs[$sensor_name]." (".$sensor_name.")";
			
			$current_units = range_SI_units($sensor_units[$sensor_name], $_SESSION['s_rerange'][$sensor_name]);
			
			$plot_y_label = "(".$current_units."/s)";
			
			make_plot($plot_name, $time, $rate, $logxy, "", $plot_y_label, 
				  $sensor_al_trip[$sensor_name],
				  $_SESSION['plot_view_x_size'], $_SESSION['plot_view_y_size'], NULL, NULL);
			
			$plot_names[] = $plot_name;
			$cut_sensor_names[] = $sensor_name;	
		      }
		  }
	      }
	  }
	else
	  {
	    $plot_names[] = "hidden";
	    $cut_sensor_names[] = $sensor_name;	
	  }  
      }
  }
mysql_close($connection);


////   This next section generate the HTML with the plot names in a table.

echo ('<TABLE border="1" cellpadding="2" frame="box" width=100%>');
for ($i = 0; $i < count($plot_names); $i++)
  {
    if ($_SESSION['s_roll_up'][$cut_sensor_names[$i]] == 0)
      {
	echo ('<TR align=center>');
	
	echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
       	echo ('<input type="hidden" name="min_all_but[]" type="submit" value='.$cut_sensor_names[$i].'>');
	echo ('<TD>');
	echo ($sensor_descs[$cut_sensor_names[$i]]);
	if ($_SESSION['show_sens_name'])
	  echo (" (".$cut_sensor_names[$i].")<br>");
	else
	  echo ("<br>");
	echo ('<input type="image" src="'.$plot_names[$i].'" border=0 align=center width='.$_SESSION['plot_view_x_size'].'>');
	echo ('</TD>');
	echo ('</FORM>');

	echo ('<TD valign="top">');	
	
	echo ('<TABLE border="0" cellpadding="0" width=100%>');
	echo ('<TR  align=center>');
	echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
	echo ('<input type="hidden" name="roll_up[]" value='.$cut_sensor_names[$i].' >');
	echo ('<TD>');
	echo ('<input type="image" src="pixmaps/Min.png" alt="Min" title="Minimize this plot">');
	echo ('</TD>');	
	echo ('</FORM>');
	echo ('</TR>');
	
	echo ('<TR>');
	echo ('<TD>');
	echo ('<br>');
	echo ('</TD>');
	echo ('</TR>');	

	echo ('<TR align=center>');
	echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
	echo ('<input type="hidden" name="log-y-button[]" value='.$cut_sensor_names[$i].' >');
	echo ('<TD>');
	echo ('<input type="image" src="pixmaps/Log_y.png" alt="Log Y" title="Log the data">');
	echo ('</TD>');
	echo ('</FORM>');
	echo ('</TR>');
	
	echo ('<TR>');
	echo ('<TD>');
	echo ('<br>');
	echo ('</TD>');
	echo ('</TR>');	

	echo ('<TR align=center>');
	echo ('<FORM action="slow_control_calc.php" method="post">');
      	echo ('<input type="hidden" name="choosen_sensor"  value='.$cut_sensor_names[$i].' >');
	echo ('<TD>');
	echo ('<input type="image" src="pixmaps/xcalc.png" alt="Calc" title="Calculations Page">');
	echo ('</TD>');
	echo ('</FORM>');
	echo ('</TR>');

	echo ('<TR>');
	echo ('<TD>');
	echo ('<br>');
	echo ('</TD>');
	echo ('</TR>');	

	if ($sensor_al_arm_val_high[$cut_sensor_names[$i]])
	  {
	    echo ('<TR align=center>');
	    echo ('<TD>');
	    echo ('High alarm:');
	    echo ('<br>');
	    echo ($sensor_al_set_val_high[$cut_sensor_names[$i]]." (".$sensor_units[$cut_sensor_names[$i]].")");
	    echo ('</TR>');
	  }
	if ($sensor_al_arm_val_low[$cut_sensor_names[$i]])
	  {
	    echo ('<TR align=center>');
	    echo ('<TD>');
	    echo ('Low alarm:');
	    echo ('<br>');
	    echo ($sensor_al_set_val_low[$cut_sensor_names[$i]]." (".$sensor_units[$cut_sensor_names[$i]].")");
	    echo ('</TD>');
	    echo ('</TR>');
	  }	
	if ($sensor_al_arm_rate_high[$cut_sensor_names[$i]])
	  {
	    echo ('<TR align=center>');
	    echo ('<TD>');
	    echo ('High rate alarm:');
	    echo ('<br>');
	    echo ($sensor_al_set_rate_high[$cut_sensor_names[$i]]." (".$sensor_units[$cut_sensor_names[$i]]."/s)");
	    echo ('</TR>');
	  }
	if ($sensor_al_arm_rate_low[$cut_sensor_names[$i]])
	  {
	    echo ('<TR align=center>');
	    echo ('<TD>');
	    echo ('Low rate alarm:');
	    echo ('<br>');
	    echo ($sensor_al_set_rate_low[$cut_sensor_names[$i]]." (".$sensor_units[$cut_sensor_names[$i]]."/s)");
	    echo ('</TD>');
	    echo ('</TR>');
	  }	
	if (!($sensor_al_arm_val_high[$cut_sensor_names[$i]]) && !($sensor_al_arm_val_low[$cut_sensor_names[$i]]) &&
	    !($sensor_al_arm_rate_high[$cut_sensor_names[$i]]) && !($sensor_al_arm_rate_low[$cut_sensor_names[$i]]))
	  {
	    echo ('<TR align=center>');
	    echo ('<TD>');
	    echo ('Alarms');
	    echo ('<br>');
	    echo ('not set.'); 
	    echo ('</TD>');
	    echo ('</TR>');
	  }

	echo ('<TR>');
	echo ('<TD>');
	echo ('<br>');
	echo ('</TD>');
	echo ('</TR>');	

	echo ('<TR align=center>');
	echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
	echo ('<input type="hidden" name="scale-y-button-up[]" value='.$cut_sensor_names[$i].' >');
	echo ('<TD>');
	echo ('<input type="image" src="pixmaps/up.png" alt="Mult. Y by 1000" title="Mult. y by 1000">');
	echo ('</TD>');
	echo ('</FORM>');
	echo ('</TR>');
	echo ('<TR align=center>');
	echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
      	echo ('<input type="hidden" name="scale-y-button-down[]" value='.$cut_sensor_names[$i].'>');
	echo ('<TD>');
	echo ('<input type="image" src="pixmaps/down.png" alt="Div. Y by 1000" title="Div. y by 1000">');
	echo ('</TD>');
	echo ('</FORM>');
	echo ('</TR>');

	//echo ('</TBODY>');
	echo ('</TABLE>');
	echo ('</TD>');
	echo ('</TR>');
      }
  }

for ($i = 0; $i < count($plot_names); $i++)
  {
    if ($_SESSION['s_roll_up'][$cut_sensor_names[$i]] != 0)  
      {
	echo ('<TR>');
	echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
	echo ('<input type="hidden" name="roll_up[]"  value='.$cut_sensor_names[$i].' >');	
	echo ('<TH>');
	if ($sensor_al_trip[$cut_sensor_names[$i]])
	  echo ('<font color="red">');
	echo ($sensor_descs[$cut_sensor_names[$i]]);
	echo ('</TH>');
	echo ('<TH>');
	echo ('<input type="image" src="pixmaps/Max.png">');	
	echo ('</TH>');
	echo ('</FORM>');
	echo ('</TR>');
      }
  }
echo ('</TABLE>');

echo ('<TABLE border="1" cellpadding="2" width=100%>');
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<TH>');
echo ('Horizontal plot size <input type="text" name="plot_view_x_size" value="'.$_SESSION['plot_view_x_size'].'" />');
echo ('</TH>');
echo ('</FORM>');

echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<TH>');
echo ('Vertical plot size <input type="text" name="plot_view_y_size" value="'.$_SESSION['plot_view_y_size'].'" />');
echo ('</TH>');
echo ('</FORM>');
echo ('</TABLE>');

echo(' </body>');
echo ('</HTML>');
?>
