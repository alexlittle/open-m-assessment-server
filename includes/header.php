<?php 
$nologinpages = array ("login","index","register");

if (!in_array($PAGE,$nologinpages)){
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
	<title><?php echo getstring("app.title");?></title>
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
				<li><form action="" method="post" name="langform" id="langform">
				<select name="lang" onchange="document.langform.submit();">
					<?php 
						foreach ($CONFIG->langs as $key => $value){
							if (isset($_SESSION["session_lang"]) &&  $_SESSION["session_lang"] == $key){
								echo "<option value='".$key."' selected='selected'>".$value."</option>";
							} else {
								echo "<option value='".$key."'>".$value."</option>";
							}
						}
					?>
				</select>
				</form></li>
			</ul>
		</div>
		<div style="clear:both"></div>
	</div>

<div id="content">