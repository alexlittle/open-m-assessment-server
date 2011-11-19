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
	
	function addUser($username,$password,$firstname,$surname,$email){
		$str = "INSERT INTO user (username,password,firstname,lastname,email) VALUES ('%s',md5('%s'),'%s','%s','%s')";
		$sql = sprintf($str,$username,$password,$firstname,$surname,$email);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		return true;
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
	
	function getQuizzes($langcode){
		$sql = "SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q 
				INNER JOIN language l ON q.quiztitleref = l.langref";
		$result = mysql_query($sql,$this->DB);
		$quizzes = array();
		while($r = mysql_fetch_object($result)){
			$q = new stdClass;
			$q->ref = $r->quiztitleref;
			$q->title = $r->langtext;
			array_push($quizzes,$q);
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
	
	function getQuiz($ref,$langcode){
		$sql = sprintf("SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q
					INNER JOIN language l ON q.quiztitleref = l.langref
					WHERE q.quiztitleref = '%s'",$ref);
		$result = mysql_query($sql,$this->DB);
		while($r = mysql_fetch_object($result)){
			$q = new stdClass;
			$q->quizid = $r->quizid;
			$q->ref = $r->quiztitleref;
			$q->title = $r->langtext;
			$q->props = array();
			$psql = sprintf("SELECT * FROM quizprop WHERE quizid = %d",$r->quizid);
			$props = mysql_query($psql,$this->DB);
			while($prop = mysql_fetch_object($props)){
				$q->props[$prop->quizpropname] = $prop->quizpropvalue;
			}
			return $q;
		}
	}
	
	function getQuizQuestions($quizid,$langcode){
		$sql = sprintf("SELECT q.questionid, q.questiontitleref, qq.orderno, l.langtext FROM question q 
						INNER JOIN quizquestion qq ON qq.questionid = q.questionid
						INNER JOIN language l ON l.langref = q.questiontitleref
						WHERE qq.quizid = %d
						ORDER BY orderno ASC",$quizid);
		$result = mysql_query($sql,$this->DB);
		$questions = array();
		while($r = mysql_fetch_object($result)){
			$q = new stdClass;
			$q->id = $r->questionid;
			$q->refid = $r->questiontitleref;
			$q->text = $r->langtext;
			$q->orderno =$r->orderno;
			$q->props = array();
			$psql = sprintf("SELECT * FROM questionprop WHERE questionid = %d",$r->questionid);
			$props = mysql_query($psql,$this->DB);
			while($prop = mysql_fetch_object($props)){
				$q->props[$prop->questionpropname] = $prop->questionpropvalue;
			}
			array_push($questions,$q);
		}
		return $questions;
	}
	
	function getQuestionResponses($questionid,$langcode){
		$sql = sprintf("SELECT r.responseid, r.responsetitleref, qr.orderno, l.langtext, r.score FROM response r 
						INNER JOIN questionresponse qr ON qr.responseid = r.responseid
						INNER JOIN language l ON l.langref = r.responsetitleref
						WHERE qr.questionid = %d
						ORDER BY orderno ASC",$questionid);
		$result = mysql_query($sql,$this->DB);
		$responses = array();
		while($o = mysql_fetch_object($result)){
			$r = new stdClass;
			$r->refid = $o->responsetitleref;
			$r->text = $o->langtext;
			$r->orderno =$o->orderno;
			$r->score = $o->score;
			array_push($responses,$r);
		}
		return $responses;
	}
	
	function addQuiz($title,$langcode){
		global $USER;
		$quiztitleref = "qt|".$USER->username."|".uniqid();
		$this->addLang($quiztitleref, $title, $langcode);
		
		$str = "INSERT INTO quiz (quiztitleref,createdby) VALUES ('%s',%d)";
		$sql = sprintf($str,$quiztitleref,$USER->userid);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		return $result;
	}
	
	function addQuestion($title,$langcode){
		global $USER;
		$questiontitleref = "qqt|".$USER->username."|".uniqid();
		$this->addLang($questiontitleref, $title, $langcode);
	
		$str = "INSERT INTO question (questiontitleref,createdby) VALUES ('%s',%d)";
		$sql = sprintf($str,$questiontitleref,$USER->userid);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		return $result;
	}
	
	function addResponse($title,$langcode,$score){
		global $USER;
		$responsetitleref = "qqrt|".$USER->username."|".uniqid();
		$this->addLang($responsetitleref, $title, $langcode);
	
		$str = "INSERT INTO response (responsetitleref,createdby,score) VALUES ('%s',%d,%d)";
		$sql = sprintf($str,$responsetitleref,$USER->userid,$score);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		return $result;
	}
	
	function addQuestionToQuiz($quizid,$questionid,$orderno){
		$str = "INSERT INTO quizquestion (quizid,questionid,orderno) VALUES (%d,%d,%d)";
		$sql = sprintf($str,$quizid,$questionid,$orderno);
		$result = mysql_query($sql,$this->DB);
		return $result;
	}
	
	function addResponseToQuestion($questionid,$responseid,$orderno){
		$str = "INSERT INTO questionresponse (questionid,responseid,orderno) VALUES (%d,%d,%d)";
		$sql = sprintf($str,$questionid,$responseid,$orderno);
		$result = mysql_query($sql,$this->DB);
		return $result;
	}
	
	function addLang($ref,$text,$langcode){
		$str = "INSERT INTO language (langref,langtext,langcode) VALUES ('%s','%s','%s')";
		$sql = sprintf($str,$ref,$text,$langcode);
		$result = mysql_query($sql,$this->DB);
	}
	
	function setProp($obj,$id,$name,$value){
		// first check to see if it exists already
		$sql = sprintf("SELECT * FROM %sprop WHERE %sid= %d AND %spropname='%s'",$obj,$obj,$id,$obj,$name);
		$result = mysql_query($sql,$this->DB);

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$updateSql = sprintf("UPDATE %sprop SET %spropvalue='%s' WHERE %sid= %d AND %spropname='%s'",$obj,$obj,$value,$obj,$id,$obj,$name);
			mysql_query($updateSql,$this->DB);
			return;
		}
		
		$insertSql = sprintf("INSERT INTO %sprop (%spropvalue, %sid,%spropname) VALUES ('%s',%d,'%s')",$obj,$obj,$obj,$obj,$value,$id,$name);
		$result = mysql_query($insertSql,$this->DB);

	}
}