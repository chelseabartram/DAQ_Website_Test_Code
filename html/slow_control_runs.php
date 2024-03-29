<?php
// slow_control_runs.php
// Part of the CLEAN slow control.  
// James Nikkel, Yale University, 2010.
// james.nikkel@yale.edu
//
//////// do this first as it re-dirrects to another page
if (isset($_POST['set_times']))
{
    $_SESSION['t_min_p'] =  $_POST['start_t'];
    if ($_POST['end_t'] == 0)
	$_SESSION['t_max_now'] = 1;
    else
    {
	$_SESSION['t_max_p'] =  $_POST['end_t'];
	$_SESSION['t_max_now'] = -1;
    }
    header('Location: slow_control_plots.php'); 
}
////////////////////////////////////////////////////////////////////////////////
session_start();
$req_priv = "basic";
include("db_login.php");
include("slow_control_page_setup.php");

if (isset($_POST['play_stuff']))
  {
    echo('<embed src="wave_files/SirenWebAlert_LUX.wav" width="300" height="90" loop="true" autostart="true" />');
  }


if (isset($_POST['new_run']))
{
    mysql_close($connection);
    include("master_db_login.php");

    if (!get_magic_quotes_gpc())   
	$_POST['note'] = addslashes($_POST['note']);
    $_POST['note'] = $_POST['note']." -- ".$_SESSION['user_name'];
    
    $query = "UPDATE `runs` SET `end_t` = '".time()."' ORDER BY `num` DESC LIMIT 1";
    $result = mysql_query($query);
    
    $query = "INSERT into `runs` (`start_t`, `note`) VALUES ('".time()."', '".$_POST['note']."')"; 
    $result = mysql_query($query);
    if (!$result)
	die ("Could not query the database <br />" . mysql_error());
}

include("aux/get_runs.php");
mysql_close($connection);

/////////////   start HTML here:
echo ('<TABLE border="1" cellpadding="4" cellspacing="2">');
echo ('<TR>');

echo ('<TH align=center>');
echo ('Plot');  
echo ('</TH>');

echo ('<TH align=center>');
echo ('Current Run #');  
echo ('</TH>');

echo ('<TH align=left>');
echo ('Start Time');  
echo ('</TH>');

echo ('<TH align=left>'); 
echo ('Note');  
echo ('</TH>');
echo ('<TBODY>');
echo ('</TR>');
echo ('<TBODY>');

echo ('<TR valign="center">');
echo ('<TD align=center>');    
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<input type="image" src="pixmaps/plot_2lines.png" name="set_times" value="1" 
        alt="Set view limits to this run." title="Set view limits to this run.">');
echo ('<input type="hidden" name="start_t" value="'.$run_start_ts[$run_nums[0]].'" >');
echo ('<input type="hidden" name="end_t" value="0" >');
echo ('</FORM>');
echo ('</TD>');
    
echo ('<TD align=center>');
echo ($run_nums[0]);
echo ('</TD>');

echo ('<TD align=left>');
echo (date("M d, Y   G:i:s", $run_start_ts[$run_nums[0]]));
echo ('</TD>');

echo ('<TD align=left>');
echo ('<PRE>');
echo ($run_notes[$run_nums[0]]);
echo ('</PRE>');
echo ('</TD>');
echo ('</TR>');    
echo ('</TABLE>');
echo ('<TABLE border="1" cellpadding="4" cellspacing="2">');

echo ('</TABLE>');
echo ('<br>');


/////  add new run if available:
if ((strpos($_SESSION['privileges'], "config") !== false) && (strpos($_SESSION['shift_status'], "Leader")))
{ 
    echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
    //echo ('New run notes: <input type="text" name="note" size = 64>');
    echo ('&#160 &#160 &#160 &#160 New run note (<100 characters): <TEXTAREA name="note" rows="1" cols="50"></TEXTAREA>');
    echo ('<input type="submit" name="new_run" value="Increment Run" title="Increment run number with this note">');
    echo ('</FORM>');
    echo ('<br>');
}


/////  search runs:
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('&#160 &#160 &#160 &#160  Seach run notes: <input type="text" name="search_text" size = 16>');
echo ('</FORM>');
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('&#160 &#160 &#160 &#160 Find run number: <input type="text" name="search_num" size = 6>');
echo ('</FORM>');

/////  list runs
echo ('<TABLE border="1" cellpadding="4" cellspacing="2">');
echo ('<TR>');

echo ('<TH align=center>');
echo ('Plot');  
echo ('</TH>');

echo ('<TH align=center>');
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<input type="image" src="pixmaps/top.png" name="run_last" value="1" title="Go to latest runs">');
echo('  ');
echo ('<input type="image" src="pixmaps/up.png" name="run_next" value="1" title="Go to more recent runs">');
echo('  ');
echo ('Run #');  
echo ('</FORM>');
echo ('</TH>');

echo ('<TH align=left>');
echo ('Start Time');  
echo ('</TH>');

echo ('<TH align=left>'); 
echo ('End Time');  
echo ('</TH>');

echo ('<TH align=left>'); 
echo ('Note');  
echo ('</TH>');
echo ('<TBODY>');
echo ('</TR>');
echo ('<TBODY>');

///////////////////////

for ($i = 1; $i < count($run_nums); $i++)
{
    echo ('<TR valign="center">');

    echo ('<TD align=center>');    
    echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
    echo ('<input type="image" src="pixmaps/plot_2lines.png" name="set_times" value="1" 
        alt="Set view limits to this run." title="Set view limits to this run.">');
    echo ('<input type="hidden" name="start_t" value="'.$run_start_ts[$run_nums[$i]].'" >');
    echo ('<input type="hidden" name="end_t" value="'.$run_end_ts[$run_nums[$i]].'" >');
    echo ('</FORM>');
    echo ('</TD>');
    
    echo ('<TD align=center>');
    echo ($run_nums[$i]);
    echo ('</TD>');
    
    echo ('<TD align=left>');
    echo (date("M d, Y   G:i:s", $run_start_ts[$run_nums[$i]]));
    echo ('</TD>');
    
    echo ('<TD align=left>');
    if ($run_end_ts[$run_nums[$i]] == 0)
	echo ('Current run');
    else
	echo (date("M d, Y   G:i:s", $run_end_ts[$run_nums[$i]]));
    echo ('</TD>');
    
    echo ('<TD align=left>');
    echo ('<PRE>');
    echo ($run_notes[$run_nums[$i]]);
    echo ('</PRE>');
    echo ('</TD>');
    echo ('</TR>');
    
}

echo ('<TR valign="center">');
echo ('<TD align=center colspan="1">');
echo ('</TD>');

echo ('<TD align=center>');   
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<input type="image" src="pixmaps/bottom.png" name="run_first" value="1" alt="Next" title="Go to first runs">');
echo (' ');
echo ('<input type="image" src="pixmaps/down.png" name="run_prev" value="1" alt="Next" title="Go to previous runs">');
echo ('</FORM>');
echo ('</TD>');

echo ('<TD align=center colspan="3">');
echo ('</TD>');
echo ('</TR>');


echo ('<TR valign="center">');
echo ('<TD align=center colspan="1">');
echo ('</TD>');

echo ('<TD align=center>');   
echo ('<FORM action="'.$_SERVER['PHP_SELF'].'" method="post">');
echo ('<input type="image" src="pixmaps/error.png" name="play_stuff" value="1">');
echo ('</FORM>');
echo ('</TD>');

echo ('<TD align=center colspan="3">');
echo ('</TD>');
echo ('</TR>');

echo ('</TABLE>');

echo(' </body>');
echo ('</HTML>');
?>
