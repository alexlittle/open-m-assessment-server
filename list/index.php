<?php
header("Content-type:text/plain;Charset:UTF-8");

$page = curPageURL();
if(endsWith($page,'/')){
	$url_prefix = $page;
} else {
	$url_prefix = dirname($page)."/";
}

$json = array();

$predata = array (	'id'=>"PreTest",
					'name'=>"PPH Pre Test",
					'url'=>$url_prefix."pretest.php");

$postdata = array (	'id'=>"PostTest",
					'name'=>"PPH Post Test",
					'url'=>$url_prefix."posttest.php");

$predataam = array(	'id'=>"PreTestAm",
					'name'=>"PPH Pre Test (Amharic)",
					'url'=>$url_prefix."pretest-am.php");

array_push($json,$predata);
array_push($json,$postdata);
array_push($json,$predataam);

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

