<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_scatter.php');
 
DEFINE('WORLDMAP','Computer.jpg');
DEFINE('DEFAULT_GFORMAT','jpg'); 
function markCallback($y,$x) {
  // Return array width
  // width,color,fill color, marker filename, imgscale
  // any value can be false, in that case the default value will
  // be used.
  // We only make one pushpin another color
  if($x==400)
    return array(false,false,false,'red',0.8);
  else
    return array(false,false,false,'green',0.8);
}
 
// Data arrays
$datax = array(10,20,30,40,50,60,70,400);
$datay = array(50,50,50,50,50,50,50,50);
 
// Setup the graph
$graph = new Graph(300,300);
 
// We add a small 1pixel left,right,bottom margin so the plot area
// doesn't cover the frame around the graph.
$graph->img->SetMargin(1,1,1,1);
$graph->SetScale('linlin',0,400,0,500);
 
// We don't want any axis to be shown
$graph->xaxis->Hide();
$graph->yaxis->Hide();
 
// Use a worldmap as the background and let it fill the plot area
$graph->SetBackgroundImage(WORLDMAP,BGIMG_FILLPLOT);
 
// Setup a nice title with a striped bevel background
$graph->title->Set("Pushpin graph");
//$graph->title->SetFont(FF_ARIAL,FS_BOLD,16);
$graph->title->SetColor('white');
$graph->SetTitleBackground('darkgreen',TITLEBKG_STYLE1,TITLEBKG_FRAME_BEVEL);
$graph->SetTitleBackgroundFillStyle(TITLEBKG_FILLSTYLE_HSTRIPED,'blue','darkgreen');
 
// Finally create the scatterplot
$sp = new ScatterPlot($datay,$datax);
 
// We want the markers to be an image
$sp->mark->SetType(MARK_IMG_PUSHPIN,'blue',0.6);
 
// Install the Y-X callback for the markers
$sp->mark->SetCallbackYX('markCallback');

// ...  and add it to the graph
$graph->Add($sp);    

$graph->Stroke("/var/www/html/jpgraph_cache/temp.png");

?>