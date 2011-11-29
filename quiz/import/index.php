<?php 

include_once('extras.php');
include_once('format.php');
include_once('gift_format.php');
header("Content-type:text/plain;charset:utf-8");
$file = "testimport.txt";
if (!file_exists($file)) {
	echo "file not found";
	die;
}

$quiz = file($file);


print_r($quiz);

print("\n\n----------------------------\n\n");

$import = new qformat_gift();
print_r($import);
print("\n\n----------------------------\n\n");

$questions = $import->readquestions($quiz);

foreach($questions as $q){
	$import->readquestion($q);
	
}
//print_r($import);
?>