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
}

?>