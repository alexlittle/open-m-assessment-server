<?php 


if ($PAGE != "login" && $PAGE != "index"){
	checkLogin();
}

$lang = optional_param("lang","",PARAM_TEXT);
if ($lang != ""){
	setLang($lang,true);
}
header("Content-type:text/html;charset:utf-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>OpenQuiz</title>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<link rel="StyleSheet" href="<?php echo $CONFIG->homeAddress; ?>/includes/style.css" type="text/css" media="screen">
	<link rel="StyleSheet" href="<?php echo $CONFIG->homeAddress; ?>/includes/printstyle.css" type="text/css" media="print">
	<link rel="shortcut icon" href="<?php echo $CONFIG->homeAddress; ?>/images/favicon.ico" />
</head>

<body>

<div id="page">
	<div id="header">
		<div id="logo">
		 	<a href="index.php">Open Quiz Logo</a>
		</div>
		<div id="menu">
			<ul>
				<li><a href="myquizzes.php">My Quizzes</a></li>
				<li><a href="results.php">My Results</a></li>
				<li><a href="newquiz.php">Create New Quiz</a></li>
			</ul>
		</div>
		<div id="userlogin">
			<ul>
				<li><a href="login.php">Login</a></li>
				<li><a href="register.php">Register</a></li>
				<li>Lang</li>
			</ul>
		</div>
		<div style="clear:both"></div>
	</div>

<div id="content">