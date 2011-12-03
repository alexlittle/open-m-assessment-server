<?php 
include_once("../config.php");
header("Content-type:text/plain;charset:utf-8");
writeToLog("info","pagehit",$_SERVER["REQUEST_URI"]);

$method = optional_param("method","",PARAM_TEXT);
$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);

/*
 * Methods with no login required
 */

if($method == 'register'){
	$email = optional_param("email","",PARAM_TEXT);
	$passwordAgain = optional_param("passwordagain","",PARAM_TEXT);
	$firstname = optional_param("firstname","",PARAM_TEXT);
	$lastname = optional_param("lastname","",PARAM_TEXT);
	
	if ($email == ""){
		echo "Enter your email";
		die;
	} else	if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $email) ) {
		echo "Invalid email address format";
		die;
	}
	// check password long enough
	if (strlen($password) < 6){
		echo "Your password must be 6 characters or more";
		die;
	}
	// check passwords match
	if ($password != $passwordAgain){
		echo "Your password don't match";
		die;
	}
	// check all fields completed
	if ($firstname == ""){
		echo "Enter your firstname";
		die;
	}
	if ($lastname == ""){
		echo "Enter your lastname";
		die;
	}
	// check username doesn't already exist
	$u = new User($email);
	$user = $API->getUser($u);
	if($user->userid != ""){
		echo "Email already registered";
		die;
	}
	
	$API->addUser($email, $password, $firstname, $lastname, $email);
	$m = new Mailer();
	$m->sendSignUpNotification($firstname." ".$lastname);
	echo "success";
	die;
}

/*
 * Login user
 */
if (!userLogin($username,$password,false)){
	echo "Login failed";
	die;
}

/*
 * Methods with login required
 */
if($method == 'downloaded'){
	$quizref = optional_param("quizref","",PARAM_TEXT);
	// login user
	
	// get quiz
	$q = $API->getQuiz($quizref);
	if($q != null){
	//mark as downloaded
		$result = $API->setQuizDownloaded($USER->userid, $q->quizid);
		if($result){
			echo "success";
		} else {
			echo "failure";
		}
	} else {
		echo "Quiz not found";
	}
	die;
}

if($method == 'downloadqueue'){
	$queue = $API->getQuizDownloadQueue($USER->userid);
	echo json_encode($queue);
	die;
}

if($method == 'list'){
	$quizzes = $API->getQuizzes();
	
	$page = curPageURL();
	if(endsWith($page,'/')){
		$url_prefix = $page;
	} else {
		$url_prefix = dirname($page)."/";
	}
	
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
			//$url = $CONFIG->homeAddress."api/?method=getquiz&ref=".$q->ref;
			$o = array(	'id'=>$q->ref,
							'name'=>$q->title,
							'url'=>$url_prefix."?method=getquiz&ref=".$q->ref);
			array_push($json,$o);
		}
	}
	echo json_encode($json);
	die;
}

if($method == 'getquiz'){
	$ref = optional_param('ref','',PARAM_TEXT);
	$quiz = $API->getQuiz($ref);
	
	// check if currently downloadable
	$downloadable = true;
	$props = $API->getQuizProps($quiz->quizid);
	if(array_key_exists('downloadable', $props)){
		if($props['downloadable'] == 'false'){
			$downloadable = false;
		}
	}
	if(!$downloadable){
		echo "Quiz not available for download";
		die;
	}
	$questions = array();
	
	$qq = $API->getQuizQuestions($quiz->quizid);
	
	foreach($qq as $q){
	
		$responses = array();
		$resps = $API->getQuestionResponses($q->id);
		
		foreach($resps as $o){
			$props = (object) $o->props;
			$r = array(
						'refid'=> $o->refid,
						'orderno'=> $o->orderno,
						'text'=>$o->text,
						'score'=>$o->score,
						'props'=>$props
			);
			array_push($responses,$r);
		}
	
		if(array_key_exists('maxscore',$q->props)){
			$score = $q->props['maxscore'];
		} else {
			$score = 0;
		}
		if(array_key_exists('type',$q->props)){
			$type = $q->props['type'];
		} else {
			$type = "multichoice";
		}
		$props = (object) $q->props;
		$newq = array(
				'refid'=>$q->refid,
				'orderno'=> $q->orderno,
				'text'=>$q->text,
				'type'=>$type,
				'props'=>$props,
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
	die;
}

if($method == 'submit'){
	$content = optional_param("content","",PARAM_TEXT);
	if($content == ""){
		echo "failure - no content";
		die;
	}
	try {
		$json = json_decode(stripslashes($content));
	
		if (isset($json->quizid)){
			$quiz = $API->getQuiz($json->quizid);
			// check if currently downloadable
			$submitable = true;
			$props = $API->getQuizProps($quiz->quizid);
			if(array_key_exists('submitable', $props)){
				if($props['submitable'] == 'false'){
					$submitable = false;
				}
			}
			if(!$submitable){
				echo "Results submissions currently disabled for this quiz";
				die;
			}
		} else {
			echo "failure";
			die;
		}
	
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
			$qar->text = $r->qrtext;
			$API->insertQuizAttemptResponse($qar);
		}
		echo "success";
	} catch (Exception $e){
		echo "failure";
	}
	die;
}

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

function endsWith($haystack, $needle){
	$length = strlen($needle);
	$start  = $length * -1; //negative
	return (substr($haystack, $start) === $needle);
}
?>