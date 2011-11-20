<?php
include_once ('../config.php');
header("Content-type:text/plain;Charset:UTF-8");

$ref = optional_param('ref','',PARAM_TEXT);
$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);
userLogin($username,$password);

writeToLog("info","pagehit",$_SERVER["REQUEST_URI"]);

$quiz = $API->getQuiz($ref);

$questions = array();

$qq = $API->getQuizQuestions($quiz->quizid);

foreach($qq as $q){
	
	$responses = array();
	$resps = $API->getQuestionResponses($q->id);
	foreach($resps as $o){
		$r = array(
					'refid'=> $o->refid,
					'orderno'=> $o->orderno,
					'text'=>$o->text,
					'score'=>$o->score	
				);
		array_push($responses,$r);
	}
	
	if(array_key_exists('maxscore',$q->props)){
		$score = $q->props['maxscore'];
	} else {
		$score = 0;
	}
	$newq = array(
			'refid'=>$q->refid,
			'orderno'=> $q->orderno,
			'text'=>$q->text,
			'score'=>$score,
			'type'=>'select1',
			'r'=>$responses
			);
	array_push($questions,$newq);
}


if(array_key_exists('maxscore',$quiz->props)){
	$maxscore = $quiz->props['maxscore'];
} else {
	$maxscore = 0;
}

$json = array (	'refid'=>$quiz->ref,
					'title'=>$quiz->title,
					'maxscore'=>$maxscore,
					'q'=>$questions);


echo json_encode($json);