<!DOCTYPE HTML>
<html manifest="mquiz.appcache">
<head>
    <title>mQuiz</title>
    <meta name="viewport" content="width=device-width, user-scalable=no" />
    <link rel="StyleSheet" href="includes/style.css" type="text/css" media="screen">
    <script type="text/javascript" src="includes/lib/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="includes/lib/jquery.validate.min.js"></script>
    <script type="text/javascript" src="includes/database.js"></script>
    <script type="text/javascript" src="includes/script.js"></script>
    <script type="text/javascript" src="includes/i8n.js"></script>
    <script type="text/javascript" src="includes/lang/en.js"></script>
    <script type="text/javascript" src="includes/quizengine.js"></script>
    
    <script type="text/javascript">
    	
    	function init(){
    		<?php 
    	    	include_once('../config.php');
    	    	if(isLoggedIn()){
    	    		printf("store.set('username','%s');",$USER->username);
    	    		printf("store.set('password','%s');",$USER->password);
    	    		
    	    	}
    	    	$uagent_obj = new uagent_info();
    	    	if(!$uagent_obj->DetectIphone() && !$uagent_obj->DetectAndroidPhone()){
    	    		printf("SOURCE = '%s';",$CONFIG->homeAddress);
    	    	}
    	    ?>
    		if($(location).attr('hash')){
    			showPage($(location).attr('hash'));
    		} else {
    			showPage('#home');
    		}
    		showUsername();
    		changeInterfaceLang();
    	}
    </script>
</head>
<body onload="init()" onhashchange="init()">
<div id="page">
	<div id="header">
		<div id="header-title" onclick="confirmExitQuiz('#home')" class="clickable">
			<h1 id="mobile_app_title" name="lang"></h1>
		</div>
		<div id="header-right">
			<div id="langchange">
				<!-- select id="changelang" onchange="changeLang()">
					<option value="EN">English</option>
				</select -->
			</div>
			<div id="logininfo">
			</div>
		</div>
		<div style="clear:both;"></div>
	</div> <!-- end #header -->
	<div id="content">
	</div> <!-- end #content -->
	<div id="footer">
		
	</div>
</div> <!-- end #page -->
</body>
</html>