<?php
include_once ('../config.php');

header("Content-type:text/plain;Charset:UTF-8");

$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);
if (!userLogin($username,$password,false)){
	echo "Login failed";
	die;
}

writeToLog("info","pagehit",$_SERVER["REQUEST_URI"]);

$quizzes = $API->getQuizzes();


$json = array();

foreach($quizzes as $q){
	
	$downloadable = true;
	$props = $API->getQuizProps($q->quizid);
	if(array_key_exists('downloadable', $props)){
		if($props['downloadable'] == 'false'){
			$downloadable = false;
		}
	}
	if($downloadable){
		$url = $CONFIG->homeAddress."api/?method=getquiz&ref=".$q->ref;
		$o = array(	'id'=>$q->ref,
						'name'=>$q->title,
						'url'=>$url);
		array_push($json,$o);
	}
	
}

echo json_encode($json);



