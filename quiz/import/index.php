<?php 

include_once('format.php');
include_once('gift_format.php');
header("Content-type:text/plain;charset:utf-8");
$file = "testimport.txt";
if (!file_exists($file)) {
	echo "file not found";
	die;
}

$quiz = file_get_contents($file);


print_r($quiz);

print("\n\n----------------------------\n\n");

$import = new qformat_gift();
print_r($import);
print("\n\n----------------------------\n\n");

$import->readquestion($quiz);
//print_r($import);
?>