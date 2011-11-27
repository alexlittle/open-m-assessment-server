<?php

define('COOKIE_DIR', '/');
define('COOKIE_MAXLIFE', '2592000');
define('GC_MAXLIFE', '2592000');

function userLogin($username,$password,$log = true){
	global $USER,$MSG;
    clearSession();
    
    if($password == ""){
    	array_push($MSG,getstring('warning.login.nopassword'));
        return false;
    }   
    
    $USER = new User($username);
    $USER->setUsername($username);
    if ($USER instanceof User)  {
            $passwordCheck = $USER->validPassword($password);
            if($passwordCheck){
                createSession($USER);
                setLang($USER->getProp('lang'));
                if($log){
                	writeToLog('info','login','user logged in');
                }
                return true;
            } else {
            	array_push($MSG,getstring('warning.login.invalid'));
            	writeToLog('info','loginfailure','username: '.$username);
            	unset($USER);
                return false;   
            }       
    } else {
        return false;   
    }   
}   


/**
 * Start a session
 *
 * @return string | false
 */ 
function startSession($ses = 'Scorecard') {
	ini_set('session.cache_expire', COOKIE_MAXLIFE);
    ini_set('session.gc_maxlifetime', GC_MAXLIFE);
    session_set_cookie_params(COOKIE_MAXLIFE, COOKIE_DIR);
    session_name($ses);
    session_start();
    
    // Reset the expiration time upon page load
    if (isset($_COOKIE[$ses])){
    	setcookie($ses, $_COOKIE[$ses], time() + COOKIE_MAXLIFE, COOKIE_DIR);
    }
}
/**
 * Clear all session variables
 * 
 */ 
function clearSession() {
    $_SESSION["session_username"] = "";  
    setcookie("user","",time()-3600, "/");                       
} 
 
 /**
  * Create the user session details.
  */
function createSession($user) {
    $_SESSION["session_username"] = $user->getUsername();
    setcookie("user",$user->getUsername(),time() + COOKIE_MAXLIFE, COOKIE_DIR);               
}

/**
 * Check that the session is active and valid for the user passed.
 */
function validateSession($username) {
	try {
		if ($_SESSION["session_username"] == $username) {
			return true;
		} else {
			return false;
	    }
	} catch(Exception $e) {
		return false;
	}		
}
 
 
 
/**
 * Checks if current user is logged in
 * if not, they get redirected to homepage 
 * 
 */
function checkLogin(){
    global $USER,$CONFIG;
    $url = "http" . ((!empty($_SERVER["HTTPS"])) ? "s" : "") . "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    if(!isset($USER->username)){
        header('Location: '.$CONFIG->homeAddress.'login.php?ref='.urlencode($url));  
        die; 
    }
}

function isLoggedIn(){
	global $USER;
	if(isset($_SESSION["session_username"]) && $_SESSION["session_username"] != ""){
		return true;
	} else {
		return false;
	}
}


