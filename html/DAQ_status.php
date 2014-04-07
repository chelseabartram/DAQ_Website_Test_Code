<html>
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

    //echo $mem_sensor_name;
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

<style type="text/css">

#fepc {
left: 10px;
top: 10px;
position:absolute;
width: 500px;
height: 300px;
}
#fepc1 {
left: 0px;
top: 0px;
position:absolute;
width:0px;
height:0px;
}
#fepc2 {
left: 0px;
top: 0px;
position:absolute;
width:0px;
height:0px;
}
#fepc3 {
left: 0px;
top: 0px;
position:absolute;
width:0px;
height:0px;
}
#fepc4 {
left: 0px;
top: 0px;
position:absolute;
width:0px;
height:0px;
}
#fepc1free {
position:relative;
top:210px;
left:45px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc2free {
position:relative;
top:230px;
left:45px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc3free {
position:relative;
top:250px;
left:45px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc4free {
position:relative;
top:270px;
left:45px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc1unsent {
position:relative;
top:110px;
left:210px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc2unsent {
position:relative;
top:130px;
left:210px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc3unsent {
position:relative;
top:150px;
left:210px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc4unsent {
position:relative;
top:170px;
left:210px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc1tosend {
position:relative;
top:210px;
left:275px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc2tosend {
position:relative;
top:230px;
left:275px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc3tosend {
position:relative;
top:250px;
left:275px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc4tosend {
position:relative;
top:270px;
left:275px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc1activeassembled {
position:relative;
top:95px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc2activeassembled {
position:relative;
top:115px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc3activeassembled {
position:relative;
top:135px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc4activeassembled {
position:relative;
top:155px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc1activestored {
position:relative;
top:185px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc2activestored {
position:relative;
top:205px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc3activestored {
position:relative;
top:225px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc4activestored {
position:relative;
top:245px;
left:420px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk1free {
position:relative;
top:105px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk2free {
position:relative;
top:125px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk3free {
position:relative;
top:145px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk4free {
position:relative;
top:165px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk1full {
position:relative;
top:5px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk2full {
position:relative;
top:25px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk3full {
position:relative;
top:45px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#mblk4full {
position:relative;
top:65px;
left:90px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc1:hover > #fepc1free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #fepc1unsent{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #fepc1tosend{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #fepc1activeassembled{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #fepc1activestored{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #mblk1free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #mblk1full{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #fepc2free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #fepc2unsent{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #fepc2tosend{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #fepc2activeassembled{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #fepc2activestored{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #mblk2free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #mblk2full{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #fepc3free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #fepc3unsent{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #fepc3tosend{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #fepc3activeassembled{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #fepc3activestored{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #mblk3free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #mblk3full{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #fepc4free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #fepc4unsent{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #fepc4tosend{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #fepc4activeassembled{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #fepc4activestored{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #mblk4free{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #mblk4full{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
div#ebpc {
left: 80px;
top: 320px;
position:absolute;
width: 500px;
height: 300px;
}
div#drpc {
left: 580px;
top: 10px;
position:absolute;
width: 500px;
height: 300px;
}
div#dataRed {
left: 510px;
top: 160px;
position:absolute;
width: 40px;
height: 600px;
}
div#eventBuild {
left: 215px;
top: 310px;
position:absolute;
width: 40px;
height: 600px;
}
div#EBPusher_EBAssembler {
border-style:0px;
left: 120px;
top: 340px;
position:absolute;
width: 60px;
height: 100px;
}
div#FE1_EB_NETPLOT {
-webkit-transform:rotate(90deg);
position:relative;
top:300px;
left:150px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE2_EB_NETPLOT {
-webkit-transform:rotate(90deg);
position:relative;
top:300px;
left:180px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE3_EB_NETPLOT {
-webkit-transform:rotate(90deg);
position:relative;
top:300px;
left:210px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE4_EB_NETPLOT {
-webkit-transform:rotate(90deg);
position:relative;
top:300px;
left:240px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE1_DR_PA_NETPLOT {
position:relative;
top:30px;
left:500px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE2_DR_PA_NETPLOT {
position:relative;
top:60px;
left:500px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE3_DR_PA_NETPLOT {
position:relative;
top:90px;
left:500px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE4_DR_PA_NETPLOT {
position:relative;
top:120px;
left:500px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE1_DR_GS_NETPLOT {
-webkit-transform:rotate(180deg);
position:relative;
top:270px;
left:600px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE2_DR_GS_NETPLOT {
-webkit-transform:rotate(180deg);
position:relative;
top:300px;
left:600px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE3_DR_GS_NETPLOT {
-webkit-transform:rotate(180deg);
position:relative;
top:330px;
left:600px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
div#FE4_DR_GS_NETPLOT {
-webkit-transform:rotate(180deg);
position:relative;
top:360px;
left:600px;
opacity:0.6;
filter:alpha(opacity=40);
z-index:2;
}
#fepc1:hover > #FE1_EB_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #FE1_DR_PA_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc1:hover > #FE1_DR_GS_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #FE2_EB_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #FE2_DR_PA_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc2:hover > #FE2_DR_GS_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #FE3_EB_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #FE3_DR_PA_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc3:hover > #FE3_DR_GS_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #FE4_EB_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #FE4_DR_PA_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}
#fepc4:hover > #FE4_DR_GS_NETPLOT{
opacity:1.0;
filter:alpha(opacity=100);
z-index:2;
}

</style>

<div id="dataRed">
<div style="position:relative;top:0px;left:0px;z-index:1">
<img src="datareduction.png" style="position:absolute"; height="100"; width="100";/>
</div>
</div>

<div id="eventBuild">
<div style="position:relative;top:0px;left:30px;z-index:2">
<img src="eventbuilder.png"; style="position:absolute"; height="80"; width="130";/>
</div>
</div>

<div id="fepc">
<div style="position:relative;top:0px;left:0px">
<img src="fepc.png" style="position:absolute"; height="300"; width="500"; />
<div id="fepc1">
<div id="fepc1free">
<img src="jpgraph_cache/memplot_FEPC1_MM_free.png" title="FEPC1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc1unsent">
<img src="jpgraph_cache/memplot_FEPC1_MM_unsent.png" title="FEPC1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc1tosend">
<img src="jpgraph_cache/memplot_FEPC1_MM_tosend.png" title="FEPC1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc1activeassembled">
<img src="jpgraph_cache/memplot_FEPC1_MM_active_assembled.png" title="FEPC1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc1activestored">
<img src="jpgraph_cache/memplot_FEPC1_MM_active_stored.png" title="FEPC1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk1free">
<img src="jpgraph_cache/memplot_MBLK1_MM_free.png" title="MBLK1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk1full">
<img src="jpgraph_cache/memplot_MBLK1_MM_full.png" title="MBLK1" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="FE1_EB_NETPLOT">
<img src="jpgraph_cache/netplot_FE1_EBPDRTNET_02IN.png" title="FEPC1"; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE1_DR_PA_NETPLOT">
<img src="jpgraph_cache/netplot_FE1_DRPDRTNET_02OUT.png" title="FEPC1" ; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE1_DR_GS_NETPLOT">
<img src="jpgraph_cache/netplot_FE1_DRGDRTNET_02IN.png" title="FEPC1" ; style="position:absolute"; height="30"; width="100";/>
</div>
</div>
</div>
<div id="fepc2">
<div id="fepc2free">
<img src="jpgraph_cache/memplot_FEPC2_MM_free.png" title="FEPC2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc2unsent">
<img src="jpgraph_cache/memplot_FEPC2_MM_unsent.png" title="FEPC2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc2tosend">
<img src="jpgraph_cache/memplot_FEPC2_MM_tosend.png" title="FEPC2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc2activeassembled">
<img src="jpgraph_cache/memplot_FEPC2_MM_active_assembled.png" title="FEPC2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc2activestored">
<img src="jpgraph_cache/memplot_FEPC2_MM_active_stored.png" title="FEPC2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk2free">
<img src="jpgraph_cache/memplot_MBLK2_MM_free.png" title="MBLK2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk2full">
<img src="jpgraph_cache/memplot_MBLK2_MM_full.png" title="MBLK2" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="FE2_EB_NETPLOT">
<img src="jpgraph_cache/netplot_FE2_EBPDRTNET_03IN.png" title="FEPC2"; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE2_DR_PA_NETPLOT">
<img src="jpgraph_cache/netplot_FE2_DRPDRTNET_03OUT.png" title="FEPC2" ; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE2_DR_GS_NETPLOT">
<img src="jpgraph_cache/netplot_FE2_DRGDRTNET_03IN.png" title="FEPC2" ; style="position:absolute"; height="30"; width="100";/>
</div>
</div>
<div id="fepc3">
<div id="fepc3free">
<img src="jpgraph_cache/memplot_FEPC3_MM_free.png" title="FEPC3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc3unsent">
<img src="jpgraph_cache/memplot_FEPC3_MM_unsent.png" title="FEPC3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc3tosend">
<img src="jpgraph_cache/memplot_FEPC3_MM_tosend.png" title="FEPC3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc3activeassembled">
<img src="jpgraph_cache/memplot_FEPC3_MM_active_assembled.png" title="FEPC3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc3activestored">
<img src="jpgraph_cache/memplot_FEPC3_MM_active_stored.png" title="FEPC3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk3free">
<img src="jpgraph_cache/memplot_MBLK3_MM_free.png" title="MBLK3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk3full">
<img src="jpgraph_cache/memplot_MBLK3_MM_full.png" title="MBLK3" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="FE3_EB_NETPLOT">
<img src="jpgraph_cache/netplot_FE3_EBPDRTNET_04IN.png" title="FEPC3"; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE3_DR_PA_NETPLOT">
<img src="jpgraph_cache/netplot_FE3_DRPDRTNET_04OUT.png" title="FEPC3"; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE3_DR_GS_NETPLOT">
<img src="jpgraph_cache/netplot_FE3_DRGDRTNET_04IN.png" title="FEPC3"; style="position:absolute"; height="30"; width="100";/>
</div>
</div>
<div id="fepc4">
<div id="fepc4free">
<img src="jpgraph_cache/memplot_FEPC4_MM_free.png" title="FEPC4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc4unsent">
<img src="jpgraph_cache/memplot_FEPC4_MM_unsent.png" title="FEPC4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc4tosend">
<img src="jpgraph_cache/memplot_FEPC4_MM_tosend.png" title="FEPC4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc4activeassembled">
<img src="jpgraph_cache/memplot_FEPC4_MM_active_assembled.png" title="FEPC4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="fepc4activestored">
<img src="jpgraph_cache/memplot_FEPC4_MM_active_stored.png" title="FEPC4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk4free">
<img src="jpgraph_cache/memplot_MBLK4_MM_free.png" title="MBLK4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="mblk4full">
<img src="jpgraph_cache/memplot_MBLK4_MM_full.png" title="MBLK4" ; style="position:absolute"; height="20"; width="50";/>
</div>
<div id="FE4_EB_NETPLOT">
<img src="jpgraph_cache/netplot_FE4_EBPDRTNET_05IN.png" title="FEPC4"; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE4_DR_PA_NETPLOT">
<img src="jpgraph_cache/netplot_FE4_DRPDRTNET_05OUT.png" title="FEPC4"; style="position:absolute"; height="30"; width="100";/>
</div>
<div id="FE4_DR_GS_NETPLOT">
<img src="jpgraph_cache/netplot_FE4_DRGDRTNET_05IN.png" title="FEPC4"; style="position:absolute"; height="30"; width="100";/>
</div>
</div>
</div>

<div id="ebpc">
<div style="position:relative;top:0px;left:0px">
<img src="ebpc.png" style="position:absolute"; height="300"; width="500"; />
</div>
<div style="position:relative;top:190px;left:75px;">
<img src="jpgraph_cache/memplot_DRPC_MM_free.png" title="DRPC"; style="position:absolute;-webkit-transform:rotate(90deg);" height="20"; width="50" />
</div>
<div style="position:relative;top:205px;left:255px;">
<img src="jpgraph_cache/memplot_DRPC_MM_free.png" title="DRPC"; style="position:absolute;" height="20"; width="50" />
</div>

</div>

<div id="drpc">
<div style="position:relative;top:0px;left:0px">
<img src="drpc.png" style="position:absolute"; height="300"; width="500"; />
</div>
<div style="position:relative;top:70px;left:320px;">
<img src="jpgraph_cache/memplot_DRPC_MM_unprocessed.png" title="DRPC"; style="position:absolute"; height="20"; width="50";/>
</div>
<div style="position:relative;top:260px;left:265px;">
<img src="jpgraph_cache/memplot_DRPC_MM_respond.png" title="DRPC"; style="position:absolute"; height="20"; width="50";/>
</div>
<div style="position:relative;top:160px;left:85px">
<img src="jpgraph_cache/memplot_DRPC_MM_free.png" title="DRPC"; style="position:absolute;-webkit-transform:rotate(90deg);" height="20"; width="50" />
</div>
</div>

</html>


