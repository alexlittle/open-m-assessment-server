<?php 
include_once("../config.php");
header("Content-type:text/plain;charset:utf-8");
$method = optional_param("method","",PARAM_TEXT);

if($method == 'register'){
	$email = optional_param("email","",PARAM_TEXT);
	$password = optional_param("password","",PARAM_TEXT);
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
		echo "Email already registered, please select another";
		die;
	}
	
	$API->addUser($email, $password, $firstname, $lastname, $email);
	echo "success";
	die;
}
?>