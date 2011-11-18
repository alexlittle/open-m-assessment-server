<?php 
/*
 * API Class
 */
class API {
	
	private $DB = false;
	   
	/*
	 * Constructor
	 */
	function api(){
	    global $CONFIG;
	    if($this->DB){
	        return $this->DB;
	    }
	    $this->DB = mysql_connect( $CONFIG->dbhost, $CONFIG->dbuser, $CONFIG->dbpass) or die('Could not connect to server.' );
	    mysql_select_db($CONFIG->dbname, $this->DB) or die('Could not select database.');
	    mysql_set_charset('utf8',$this->DB); 
	    return $this->DB;
	}
	
	function cleanUpDB(){
		if( $this->DB != false ){
			mysql_close($this->DB);
		}
		$this->DB = false;
	}
	
	function getUser($user){
		$sql = "SELECT * FROM user WHERE username ='".$user->username."' LIMIT 0,1";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($row = mysql_fetch_array($result)){
			$user->userid = $row['userid'];
			$user->username = $row['username'];
			$user->firstname = $row['firstname'];
			$user->lastname =  $row['lastname'];
		}
		return $user;
	}
	
	function getUsers(){
		$sql = "SELECT * FROM user u
					INNER JOIN healthpoint hp ON hp.hpid = u.hpid
					ORDER BY u.firstname";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$users = array();
		while($row = mysql_fetch_object($result)){
			array_push($users,$row);
		}
		return $users;
	}
	
	function getUserProperties(&$user){
		$sql = "SELECT * FROM userprops WHERE userid=".$user->userid;
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$user->props[$row['propname']] = $row['propvalue'];
		}
	}
	
	function setUserProperty($userid,$name,$value){
		// first check to see if it exists already
		$sql = sprintf("SELECT * FROM userprops WHERE userid= %d AND propname='%s'",$userid,$name);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$updateSql = sprintf("UPDATE userprops SET propvalue='%s' WHERE userid= %d AND propname='%s'",$value,$userid,$name);
			$result = _mysql_query($updateSql,$this->DB);
			if (!$result){
				writeToLog('error','database',$sql);
			}
			return;
		}
	
		$insertSql = sprintf("INSERT INTO userprops (propvalue, userid,propname) VALUES ('%s',%d,'%s')",$value,$userid,$name);
		$result = _mysql_query($insertSql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
		}
	}
	
	function userValidatePassword($username,$password){
		global $USER;
		$sql = sprintf("SELECT userid FROM user WHERE username='%s' AND password=md5('%s')",$username,$password);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			return true;
		}
		return false;
	}
	
	function userChangePassword($newpass){
		global $USER;
		$sql = sprintf("UPDATE user SET password = md5('%s') WHERE userid=%d",$newpass,$USER->userid);
		$result = _mysql_query($sql,$this->DB);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 *
	*/
	function writeLog($loglevel,$userid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries){
		$sql = sprintf("INSERT INTO log (loglevel,userid,logtype,logmsg,logip,logpagephptime,logpagemysqltime,logpagequeries) VALUES ('%s',%d,'%s','%s','%s',%f,%f,%d)", $loglevel,$userid,$logtype,mysql_real_escape_string($logmsg),$ip,$logpagephptime,$logpagemysqltime,$logpagequeries);
		_mysql_query($sql,$this->DB);
	}
	
	function insertQuizAttempt($qa){
		$sql = sprintf("INSERT INTO quizattempt (quizref,qadate,qascore,qauser,submituser, maxscore) 
					VALUES ('%s',%d, %d, '%s', '%s',%d)",
					$qa->quizref,
					$qa->quizdate,
					$qa->userscore,
					$qa->username,
					$qa->submituser,
					$qa->maxscore);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		return $result;
	}
	
	function insertQuizAttemptResponse($qar){
		$sql = sprintf("INSERT INTO quizattemptresponse (qaid,responserefid,questionrefid,qarscore) 
					VALUES (%d, '%s', '%s', %d)",
					$qar->qaid,
					$qar->questionResponseRef,
					$qar->questionRef,
					$qar->userScore);
		mysql_query($sql,$this->DB);
	}
	
	function getQuizzes(){
		$sql = "SELECT DISTINCT quizref FROM quizattempt
					WHERE quizref != ''";
		$result = mysql_query($sql,$this->DB);
		$quizzes = array();
		while($r = mysql_fetch_object($result)){
			array_push($quizzes,$r->quizref);
		}
		return $quizzes;
	}
	function getQuizScores($quizref){
		$sql = sprintf("SELECT Count(*) as NoScores, qascore*100/maxscore as scorepercent FROM quizattempt
					WHERE quizref = '%s'
					group by qascore",$quizref);
		$result = mysql_query($sql,$this->DB);
		$resp = array();
		while($r = mysql_fetch_object($result)){
			$resp[$r->scorepercent] = $r->NoScores; 
		}
		return $resp;
	}
	function getQuizAvgResponseScores($quizref){
		$sql = sprintf("SELECT AVG(qarscore) as avgscore,  questionrefid FROM quizattemptresponse qar
				INNER JOIN quizattempt qa ON qa.id = qar.qaid
				WHERE quizref = '%s'
				GROUP BY questionrefid",$quizref);
		$result = mysql_query($sql,$this->DB);
		$resp = array();
		while($r = mysql_fetch_object($result)){
			$resp[$r->questionrefid] = $r->avgscore;
		}
		return $resp;
	}
}