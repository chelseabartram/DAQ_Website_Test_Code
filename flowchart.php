<?php
require_once ("jpgraph/jpgraph.php");
require_once ("jpgraph/jpgraph_scatter.php");
$indicators_on=0;
function flow_chart($plot_name,$queue_data,$num_fields,$row_length)
{

  function markCallback($y,$x) {
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

  DEFINE ('BACKGROUND','Computer.jpg');

  $datax = array(10,20,30,40,50,60,70,80,90,100);
  $datay = array(50,50,50,50,50,50,50,50,50,50);
  
  $graph = new Graph(400,270);
  $graph->SetScale('linlin',0,100,0,100);
  $graph->xaxis->Hide();
  $graph->yaxis->Hide();

  echo "<br>";
  $tot_num_blocks=20000;
  for( $i=1; $i<($num_fields-2); $i++)
    {
      $sum=0;
      $blocktype=array();
      for($j=0; $j<$row_length; $j++)
	{
	  $blocktype[$j] = $queue_data[$i][$j];
	  $sum+=$blocktype[$j];
	}
      echo $sum;
      echo " ";
      $queue = new ScatterPlot($datay,$datax);
      $queue->mark->SetType(MARK_IMG_PUSHPIN,'blue',0.6);
      $average=$sum/$row_length;
      echo $average;
      echo " ";
      $percent_full=($average/$tot_num_blocks)*100.0;
      echo $percent_full;
      echo "<br>";
      global $indicators_on;
      $indicators_on=round($percent_full,-1);
      echo $indicators_on;
      echo "<br>";
      $queue->mark->SetCallbackYX('markCallback');
      $graph->Add($queue);
      $graph->Stroke("/var/www/html/jpgraph_cache/newtest.png");
    }

}

?>