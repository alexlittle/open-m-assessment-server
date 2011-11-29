<?php 

include_once('extras.php');
include_once('format.php');
include_once('gift_format.php');
header("Content-type:text/html;charset:utf-8");
$file = "testimport.txt";
if (!file_exists($file)) {
	echo "file not found";
	die;
}

$quiz = file($file);

echo "<pre>";
print_r($quiz);
echo "</pre>";

print("<hr/>");

$import = new qformat_gift();
echo "<h2>GIFT Object</h2><pre>";
print_r($import);
echo "</pre>";
print("<hr/>");

$questions = $import->readquestions($quiz);

foreach($questions as $q){
	echo "<h2>".$q->questiontext."</h2><pre>";
	print_r($q);
	echo "</pre>";
	print("<hr/>");
//	echo $import->writequestion($q);
	
}
//print_r($import);
?>