<?php
require_once ("jpgraph/jpgraph.php");
require_once ("jpgraph/jpgraph_scatter.php");

$indicators_on=0;

function markCallbackNet($y,$x) {
  // Return array width
  // width,color,fill color, marker filename, imgscale
  // any value can be false, in that case the default value will
  // be used.
  // We only make one pushpin another color
    global $indicators_on;
    if( $x <= $indicators_on ) 
      return array(false,false,false,'red',0.8);
    else
      return array(false,false,false,'green',0.8);
}
function markCallbackInvertedNet($y,$x) {
  // Return array width
  // width,color,fill color, marker filename, imgscale
  // any value can be false, in that case the default value will
  // be used.
  // We only make one pushpin another color
    global $indicators_on;
    if( $x <= $indicators_on ) 
      return array(false,false,false,'green',0.8);
    else
      return array(false,false,false,'red',0.8);
}


function net_flow_chart($plot_name,$queue_data,$num_fields,$row_length,$field_names)
{
  //  echo $plot_name;
  //  echo "<br>";
  DEFINE ('NBACKGROUND','net_arrow.jpg');

  $datax = array(10,20,30,40,50,60,70,80,90,100);
  $datay = array(50,50,50,50,50,50,50,50,50,50);

  $graph = new Graph(400,100);
  $graph->img->SetMargin(1,1,1,1);
  $graph->SetScale('linlin',0,100,0,110);
  $graph->xaxis->Hide();
  $graph->yaxis->Hide();
  $graph->SetBackgroundImage(NBACKGROUND,BGIMG_FILLPLOT);

  //Would be good to query db for this
   $tot_num_blocks=100;

   $file_name=$plot_name . ".png";

  //There is only one column of interest for the netplots! It is 'value'.
  $i=1;

  $sum=0;
  $blocktype=array();
  for($j=0; $j<$row_length; $j++)
    {
      $blocktype[$j] = $queue_data[$i][$j];
      $sum+=$blocktype[$j];
    }
  
  $queue = new ScatterPlot($datay,$datax);
  $queue->mark->SetType(MARK_IMG_LBALL,'blue',0.6);
  $average=$sum/$row_length;

  $percent_full=($average/$tot_num_blocks)*100.0;
  /*echo $plot_name;
  echo " ";
  echo $sum;
  echo " ";
  echo $average;
  echo " ";
  echo $percent_full;
  echo "<br>";*/
  
  global $indicators_on;
  $indicators_on=round($percent_full,-1);
  $queue->mark->SetCallbackYX('markCallback');
  $graph->Add($queue);
  $graph->Stroke("/var/www/html/jpgraph_cache/".$file_name);
  
return 0;
}
?>