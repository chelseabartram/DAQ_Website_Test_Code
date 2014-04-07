<?php
include ("jpgraph/jpgraph.php");	
include	("jpgraph/jpgraph_line.php");
include ("jpgraph/jpgraph_log.php");
include ("jpgraph/jpgraph_plotline.php");
include ("jpgraph/jpgraph_scatter.php");

function make_plot($plot_name, $x_data, $y_data, $logxy, $title, 
		   $y_label, $alarm_trip,
		   $x_size, $y_size, $y_reg, $v_line)
{
    $n=count($x_data);

    $x_min = $_SESSION['t_min_p'];
    $x_max = $_SESSION['t_max_p'];
 
    if (strcmp($logxy, "intlog") != 0)
    {
	$y_min = min($y_data);
	$y_max = max($y_data);
	$dy = $y_max-$y_min;
	$y_min -= 0.02*$dy;
	$y_max += 0.02*$dy;
    }
    else
    {
	$y_min = 0;
	$y_max = 0;
    }

    $graph = new Graph($x_size, $y_size, "auto", 60);
    
    $graph->SetScale($logxy, $y_min, $y_max, $x_min, $x_max);
    $graph->img->SetMargin(100, 5, 10, 80);  
    $graph ->xgrid->Show(true);
    $graph ->ygrid->Show(true);
    $graph->xaxis->title->Set("time (s)");
    $graph->yaxis->title->Set($y_label);
    $graph->xaxis->Setcolor($_SESSION['textcolour']);
    $graph->yaxis->Setcolor($_SESSION['textcolour']);
    $graph->xaxis->SetTitleMargin(50);
    $graph->yaxis->SetTitleMargin(70);
    

    $graph->SetFrame(true,$_SESSION['bgcolour'],0);

    if ($title != "")
      {
	$graph->title->Set($title);
	$graph->title->SetColor($_SESSION['textcolour']);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);	
      }

    if ($alarm_trip)   
    {  
	$graph->SetColor("lightred");
	$graph->SetMarginColor("red");
	$graph->xaxis->Setcolor("black");
	$graph->yaxis->Setcolor("black");
	$graph->title->SetColor("black");
	$graph->subtitle->Set("Alarm Tripped!");
	$graph->subtitle->SetColor("white");
    }
    else
    {
	$graph->SetFrame(true,$_SESSION['bgcolour'], 1);
	$graph->SetBackgroundGradient('darkblue','blue', GRAD_MIDHOR, BGRAD_PLOT);
	$graph->SetColor("darkblue");
	$graph->SetMarginColor($_SESSION['bgcolour']);
	$graph ->xgrid->SetColor("black");
	$graph ->ygrid->SetColor("black");
    }
    $graph->xaxis->SetPos("min");

    $lineplot = new ScatterPlot($y_data, $x_data);
    if (count($x_data) < 70)                    ////   if there are less than 70 points, add markers to plot.
    {
	$lineplot->mark->SetType(MARK_UTRIANGLE);
	$lineplot->mark->SetFillColor("red");
	
    }
    else if (count($x_data) > 100)
    {
	$lineplot->SetFastStroke();
    }
    //Make all the markers red
    $lineplot->mark->SetType(MARK_UTRIANGLE);
    $lineplot->mark->SetFillColor("red");

    $lineplot->SetColor("yellow");
    $lineplot->SetWeight(2);

    if (max($x_data)-$x_data[0] > 3*3600*24)
    {
	$graph->xaxis->SetLabelFormatCallback('DateCallback');
	$graph->xaxis->title->Set("date");
    }
    else
    {
	$graph->xaxis->SetLabelFormatCallback('TimeCallback');
	$lineplot->SetLegend(DateCallback($x_data[0]));
	$graph->xaxis->title->Set("time");
    }
    $graph->xaxis->SetLabelAngle(90);

    $graph->Add($lineplot);
    $graph->legend->Pos(0.01,0.96,"left","center");
    $graph->legend->SetMarkAbsSize(0);

    if (!empty($_SESSION['go_msg_time']))
    {
	$line_mesg = new PlotLine(VERTICAL, $_SESSION['go_msg_time'], "red", 2);
	$graph->AddLine($line_mesg);
    }

    if (!empty($y_reg))
    {
	$x_reg=array(min($x_data), max($x_data)); 
	$linereg = new LinePlot(array($y_reg[0], $y_reg[1]), $x_reg);	
	$linereg->SetColor("green");
	$graph->AddLine($linereg);
    }

    if (!empty($v_line))
    {
	$line_mesg = new PlotLine(VERTICAL, $v_line, "green", 2);
	$graph->AddLine($line_mesg);
    }
    
    $graph->Stroke($plot_name);
    return $plot_name;
}

function make_phase_plot($plot_name, $x_data, $y_data, $title, 
			 $y_label, $alarm_trip, 
			 $x_size, $y_size, $type)
{
    if (strcmp($type, "Ar") == 0)
    {
	$x_min = 0;
	$x_max = 100;
	$y_min = 0;
	$y_max = 1;
    }
    else if (strcmp($type, "N") == 0)
    {
	$x_min = 0;
	$x_max = 100;
	$y_min = 0;
	$y_max = 1;	
    }
    else if (strcmp($type, "Ne") == 0)
    {
    	$x_min = 0;
	$x_max = 100;
	$y_min = 0;
	$y_max = 1;	
    }
    else
	return null;
    

    $n=count($x_data);

    $graph = new Graph($x_size, $y_size, "auto", 60);
    
    $graph->SetScale('linlin', $y_min, $y_max, $x_min, $x_max);
    $graph->img->SetMargin(100,5,30,80);  
    $graph ->xgrid->Show(true);
    $graph ->ygrid->Show(true);
    $graph->yaxis->title->Set($y_label);
    $graph->xaxis->SetTitleMargin(50);
    $graph->yaxis->SetTitleMargin(70);
    
    $graph->SetFrame(true,$_SESSION['bgcolour'],0);

    $graph->title->Set($title);
    $graph->title->SetFont(FF_FONT1,FS_BOLD);	
    
    if ($alarm_trip)   
    {  
	$graph->SetColor("lightred");
	$graph->SetMarginColor("red");
	$graph->subtitle->Set("Alarm Tripped!");
	$graph->subtitle->SetColor("white");
    }
    else
    {
	$graph->SetFrame(true,$_SESSION['bgcolour'], 1);
	$graph->SetBackgroundGradient('darkblue','blue', GRAD_MIDHOR, BGRAD_PLOT);
	$graph->SetColor("darkblue");
	$graph->SetMarginColor($_SESSION['bgcolour']);
	$graph ->xgrid->SetColor("black");
	$graph ->ygrid->SetColor("black");
    }
    $graph->xaxis->SetPos("min");

    $lineplot = new LinePlot($y_data, $x_data);
    if (count($x_data) < 70)                    ////   if there are less than 70 points, add markers to plot.
    {
	$lineplot->mark->SetType(MARK_UTRIANGLE);
	$lineplot->mark->SetFillColor("red");
	
    }
    else if (count($x_data) > 1000)
    {
	$lineplot->SetFastStroke();
    }
    
    $lineplot->SetColor("yellow");
    $lineplot->SetWeight(2);

    if (max($x_data)-$x_data[0] > 3*3600*24)
	$graph->xaxis->SetLabelFormatCallback('DateCallback');
    else
    {
	$graph->xaxis->SetLabelFormatCallback('TimeCallback');
	$lineplot->SetLegend(DateCallback($x_data[0]));
    }
    $graph->xaxis->SetLabelAngle(90);

    $graph->Add($lineplot);
    $graph->legend->Pos(0.15,0.1,"left","bottom");

    if (!empty($_SESSION['go_msg_time']))
    {
	$line_mesg = new PlotLine(VERTICAL, $_SESSION['go_msg_time'], "red", 2);
	$graph->AddLine($line_mesg);
    }

    if (!empty($y_reg))
    {
	$x_reg=array(min($x_data), max($x_data)); 
	$linereg = new LinePlot(array($y_reg[0], $y_reg[1]), $x_reg);	
	$linereg->SetColor("green");
	$graph->AddLine($linereg);
    }

    if (!empty($v_line))
    {
	$line_mesg = new PlotLine(VERTICAL, $v_line, "green", 2);
	$graph->AddLine($line_mesg);
    }
    
    $graph->Stroke($plot_name);
    return $plot_name;
}
function make_memplot($plot_name, $y_data_array, $logxy, $x_size, $y_size, $num_fields, $sensor_name, $x_data, $row_length, $legend_names, $colors)
{
  $x_min = $_SESSION['t_min_p'];
  $x_max = $_SESSION['t_max_p'];

  $interval=($x_max-$x_min) / 10.0;

  $graph = new Graph($x_size, $y_size, "auto");
  $graph->SetScale($logxy,0,0,$x_min,$x_max);
  $graph->img->SetMargin(200, 5, 10, 80);  
  $graph ->xgrid->Show(true);
  $graph ->ygrid->Show(true);
  $graph->xaxis->title->Set("time (minutes)");
  $graph->yaxis->title->Set("Number of Blocks");
  $graph->xaxis->SetTitleMargin(50);
  $graph->yaxis->SetTitleMargin(70);
  $graph->xaxis->SetLabelAngle(90);
  $graph->SetFrame(true,$_SESSION['bgcolour'],0);
  $graph->xaxis->Setcolor($_SESSION['textcolour']);
  $graph->yaxis->Setcolor($_SESSION['textcolour']);
  $graph->SetBackgroundGradient('darkblue','blue', GRAD_MIDHOR, BGRAD_PLOT);
  $graph->SetColor("darkblue");
  $graph->SetMarginColor($_SESSION['bgcolour']);
  $graph ->xgrid->SetColor("black");
  $graph ->ygrid->SetColor("black");

  if ($title != "")
    {
      $graph->title->Set($title);
      $graph->title->SetColor($_SESSION['textcolour']);
      $graph->title->SetFont(FF_FONT1,FS_BOLD);	
    }
   $graph->xaxis->SetPos("min");

  //This code gets each column of the large array called $y_data_array and puts these columns into separate arrays. These separate arrays will be put into separate scatterplots within the for loop.
  
  //Define an array of colors to make each scatterplot a unique color within the for loop. Colors come from the mysql database.
  $color_array = array();
  //PHP arrays are maps so you can define keys like so. No blue because that's the background color.
  $color_array = array("rd"=>"red", "or"=>"orange", "yl"=>"yellow", "gr"=>"green", "cy"=>"cyan", "vi"=>"violet", "pk"=>"pink", "br"=>"brown");
    
  //Groups two sequential letters in the array "colors" together 
  $j=0;
  for($i=0;$i < strlen($colors);$i+=2)
    {
      $new_colors[$j]=$colors[$i].$colors[$i+1];
      $j++;
    }
  
  //Loop to define a separate "column" array for each of the column names (this is the type of input that jpgraph wants)
  for( $i=0; $i<($num_fields-1); $i++)
    {
      $blocktype = array();
      for($j=0; $j<$row_length; $j++)
	{
	  $blocktype[$j] = $y_data_array[$i][$j];
	}
      //Create a scatterplot for each "column" array
      $scatterplot = new ScatterPlot($blocktype,$x_data);
      $new_color = $new_colors[$i];
      $color = $color_array[$new_color];
      //Give each scatterplot a unique color
      $scatterplot -> SetColor($color);
      //Loop over the names in the legend_names array (start at $i+1 to exclude time)
      $scatterplot_name=$legend_names[($i+1)];
      $scatterplot -> SetLegend($scatterplot_name);
      $scatterplot->mark->SetType(MARK_FILLEDCIRCLE);
      $scatterplot->mark->SetFillColor($color);
      //Add each scatterplot to the graph.
      $graph->Add($scatterplot);
    }
 
  //Make time human-readable
  $graph->xaxis->SetLabelFormatCallback('TimeCallback');
  //Add Legend
  $graph->legend->Pos(0.82,0.73,'right','center');
  $graph->legend->SetColor('blue');
  $graph->Stroke($plot_name);
  return $plot_name;
}

?>
