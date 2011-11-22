<?php
include_once ('../config.php');

header("Content-type:text/plain;Charset:UTF-8");

$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);
if (!userLogin($username,$password)){
	echo "Login failed";
	die;
}

writeToLog("info","pagehit",$_SERVER["REQUEST_URI"]);

$quizzes = $API->getQuizzes();

$page = curPageURL();
if(endsWith($page,'/')){
	$url_prefix = $page;
} else {
	$url_prefix = dirname($page)."/";
}

$json = array();

foreach($quizzes as $q){
	$o = array(	'id'=>$q->ref,
					'name'=>$q->title,
					'url'=>$url_prefix."getquiz.php?ref=".$q->ref);
	array_push($json,$o);
	
}

echo json_encode($json);


function curPageURL() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function endsWith($haystack, $needle)
{
	$length = strlen($needle);
	$start  = $length * -1; //negative
	return (substr($haystack, $start) === $needle);
}

