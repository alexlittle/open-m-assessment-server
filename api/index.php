<?php 
include_once("../config.php");

$format = optional_param("format","plain",PARAM_TEXT);
if($format == 'json'){
	header('Content-type: application/json; charset=UTF-8');
} else {
	header("Content-type:text/plain;charset:utf-8");
}
writeToLog("info","pagehit",$_SERVER["REQUEST_URI"]);

$method = optional_param("method","",PARAM_TEXT);
$username = optional_param("username","",PARAM_TEXT);
$password = optional_param("password","",PARAM_TEXT);


$response = new stdClass();

/*
 * Methods with no login required
 */

if($method == 'register'){
	$email = optional_param("email",$username,PARAM_TEXT);
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
		if($format == 'json'){
			$response->error = "Email already registered";
			echo json_encode($response);
		} else {
			echo "Email already registered";
		}
		die;
	}
	
	$API->addUser($email, $password, $firstname, $lastname, $email);
	$m = new Mailer();
	$m->sendSignUpNotification($firstname." ".$lastname);
	
	if($format == 'json'){
		$login = userLogin($username,$password,false);
		$response->login = $login;
		$response->hash = md5($password);
		$response->name = $USER->firstname + " "+ $USER->lastname;
		echo json_encode($response);
	} else {
		echo "success";
	}
	die;
}


if($method == 'login'){
	$login = userLogin($username,$password,false);
	$response->login = $login;
	$response->hash = md5($password);
	$response->name = $USER->firstname ." " .$USER->lastname;
	echo json_encode($response);
	die;
}

if($method == 'search'){
	$t = optional_param("t","",PARAM_TEXT);
	
	if($t == ""){
		$response->error = "No search terms provided";
		echo json_encode($response);
		die;
	}
	
	$results = $API->searchQuizzes($t);
	echo json_encode($results);
	die;
}

/*
 * Login user
 */
if (!userLogin($username,$password,false)){
	if($format == 'json'){
		$response->login = false;
		echo json_encode($response);
	} else {
		echo "Login failed";
	}
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
			$o = array(	'id'=>$q->ref,
							'name'=>$q->title,
							'url'=>$url_prefix."?method=getquiz&ref=".$q->ref);
			array_push($json,$o);
		}
	}
	echo json_encode($json);
	die;
}

if($method == 'suggest'){
	$quizzes = $API->suggestQuizzes();
	echo json_encode($quizzes);
	die;
}

if($method == 'getquiz'){
	$ref = optional_param('ref','',PARAM_TEXT);
	$quiz = $API->getQuiz($ref);
	
	if($quiz == null){
		if($format == 'json'){
			$response->error = "Quiz not found";
			echo json_encode($response);
		} else {
			echo "Quiz not found";
		}
		die;
	}
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
		$response->result = false;
		echo json_encode($response);
		die;
	}
	$json = json_decode(stripslashes($content));
	if(is_array($json)){
		foreach($json as $i){
			saveResult($i,$username);
		}
	} else {
		saveResult($json,$username);
	}
	
	if($format == 'json'){
		$response->result = true;
		echo json_encode($response);
	} else {
		echo 'success';
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

function saveResult($json,$username){
	global $API;
	try{
		if (isset($json->quizid)){
			$quiz = $API->getQuiz($json->quizid);
		} else {
			return false;
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
		return true;
	} catch (Exception $e){
		return false;
	}
}
?>