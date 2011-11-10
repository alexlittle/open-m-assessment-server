<?php
include_once("../config.php");

$receipts_dir = "../receipts/";
header("Content-type:text/plain;charset:utf-8");

$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);
$content = optional_param("content","",PARAM_TEXT);

if($username == "" || $password == "" || $content == ""){
	echo "failure";
	die;
}
$date = date('l jS \of F Y h:i:s A')."\n";

$myFile = $receipts_dir."submit.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh,$date);
fwrite($fh,$username);
fwrite($fh, stripslashes($content));
fclose($fh);

try {
	$json = json_decode(stripslashes($content));
	
	$qa = new QuizAttempt();
	$qa->quizref = $json->quizid;
	$qa->username = $json->username;
	$qa->maxscore = $json->maxscore;
	$qa->userscore = $json->userscore;
	$qa->quizdate = $json->quizdate;
	$qa->submituser = $username;
	
	// insert to quizattempt
	$newId = $API->insertQuizAttempt($qa);
	
	$responses = $json->responses;
	foreach ($responses as $r){
		$qar = new QuizAttemptResponse();
		$qar->qaid = $newId;
		$qar->userScore = $r->score;
		$qar->questionRef = $r->qid;
		$qar->questionResponseRef = $r->qrid;
		$API->insertQuizAttemptResponse($qar);
	}
	echo "success";
} catch (Exception $e){
	echo "failure";
}






