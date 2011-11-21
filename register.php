<?php
include_once("config.php");
global $PAGE,$MSG,$API;
$PAGE = "register";
include_once("./includes/header.php");

$password = optional_param("password","",PARAM_TEXT);
$repeatpassword = optional_param("repeatpassword","",PARAM_TEXT);
$firstname = optional_param("firstname","",PARAM_TEXT);
$surname = optional_param("surname","",PARAM_TEXT);
$email = optional_param("email","",PARAM_TEXT);
$submit = optional_param("submit","",PARAM_TEXT);

if ($submit != ""){
	if ($email == ""){
		array_push($MSG,"Enter your email");
	} else	if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $email) ) {
		array_push($MSG,"Invalid email address format");
	} 
	
	// check password long enough
	if (strlen($password) < 6){
		array_push($MSG,"Your password must be 6 characters or more");
	}
	// check passwords match
	if ($password != $repeatpassword){
		array_push($MSG,"Your password don't match");
	}
	// check all fields completed
	if ($firstname == ""){
		array_push($MSG,"Enter your firstname");
	}
	if ($surname == ""){
		array_push($MSG,"Enter your surname");
	}
	
	// check username doesn't already exist
	$u = new User($email);
	$user = $API->getUser($u);
	if($user->userid != ""){
		array_push($MSG,"Username/email already in use, please select another");
	}
	
	// create user
	if(count($MSG) == 0){
		if($API->addUser($email, $password, $firstname, $surname, $email)){
			echo "You are now registered, please <a href='login.php'>login</a>";
			include_once("./includes/footer.php");
			die;
		} else {
			array_push($MSG,"Registration failure - sorry!");
		}
	}
	
}

echo "<h1>".getstring("register.title")."</h1>";

if(!empty($MSG)){
	echo "<ul>";
	foreach ($MSG as $err){
		echo "<li>".$err."</li>";
	}
	echo "</ul>";
}
?>


<form method="post" action="">
<div class="formblock">
	<div class="formlabel"><?php echo getstring('register.email'); ?></div>
		<div class="formfield"><input type="text" name="email" value="<?php echo $email; ?>"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("register.password"); ?></div>
		<div class="formfield"><input type="password" name="password" value="<?php echo $password; ?>"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("register.repeatpassword"); ?></div>
		<div class="formfield"><input type="password" name="repeatpassword" value="<?php echo $repeatpassword; ?>"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("register.firstname"); ?></div>
		<div class="formfield"><input type="text" name="firstname" value="<?php echo $firstname; ?>"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel"><?php echo getstring("register.surname"); ?></div>
		<div class="formfield"><input type="text" name="surname" value="<?php echo $surname; ?>"></input></div>
	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield"><input type="submit" name="submit" value="<?php echo getstring("register.submit.button"); ?>"></input></div>
	</div>
	
</form>

<?php 
include_once("./includes/footer.php");
?>