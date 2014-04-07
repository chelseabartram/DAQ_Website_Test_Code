<?php
$imageArray = array();
$imageMap = array();
/*Search for all files that match this pathname*/
$imageArray = glob("*a.png");

/*Counts all the images in an array*/
$imArraySize = count($imageArray);
for($i=0;$i<$imArraySize;$i++)
  {
    $temp=$imageArray[$i];
    /*Break the file name up by the _ delimiter*/
    $pieces=explode("_",$temp);
    /*Break the file name up by the . delimiter*/
    $smallpieces=explode(".",$pieces[1]);
    $num=(int)$smallpieces[0];
    echo $num;
    echo " ";
    $imageMap[$num]=$temp;
  }

/*Sorts an array by key*/
ksort($imageMap);
echo "\n";
echo "The file name with the smallest number is";
echo " ";
echo reset($imageMap);
echo key($imageMap);
echo ".";
echo "\n";
?>