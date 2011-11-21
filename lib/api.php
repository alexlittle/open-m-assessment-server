<?php 

define('DEFAULT,DAYS',14);

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
			writeToLog('error','database',$sql);
			return false;
		}
	}
	
	/*
	 *
	*/
	function writeLog($loglevel,$userid,$logtype,$logmsg,$ip,$logpagephptime,$logpagemysqltime,$logpagequeries){
		$sql = sprintf("INSERT INTO log (loglevel,userid,logtype,logmsg,logip,logpagephptime,logpagemysqltime,logpagequeries) VALUES ('%s',%d,'%s','%s','%s',%f,%f,%d)", $loglevel,$userid,$logtype,mysql_real_escape_string($logmsg),$ip,$logpagephptime,$logpagemysqltime,$logpagequeries);
		mysql_query($sql,$this->DB);
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
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		return $result;
	}
	
	function insertQuizAttemptResponse($qar){
		$sql = sprintf("INSERT INTO quizattemptresponse (qaid,responserefid,questionrefid,qarscore) 
					VALUES (%d, '%s', '%s', %d)",
					$qar->qaid,
					$qar->questionResponseRef,
					$qar->questionRef,
					$qar->userScore);
		$result = mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
	}
	
	function getQuizzes(){
		$sql = "SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q 
				INNER JOIN language l ON q.quiztitleref = l.langref";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$quizzes = array();
		while($r = mysql_fetch_object($result)){
			$q = new stdClass;
			$q->ref = $r->quiztitleref;
			$q->title = $r->langtext;
			array_push($quizzes,$q);
		}
		return $quizzes;
	}
	
	function getQuizzesForUser($userid){
		$sql = sprintf("SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q
					INNER JOIN language l ON q.quiztitleref = l.langref
					WHERE q.createdby = %d
					ORDER BY l.langtext ASC",$userid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$quizzes = array();
		while($r = mysql_fetch_object($result)){
			$attempts = $this->getQuizNoAttempts($r->quiztitleref);
			$q = new stdClass;
			$q->ref = $r->quiztitleref;
			$q->title = $r->langtext;
			$q->noattempts = $attempts->noattempts;
			$q->avgscore = $attempts->avgscore;
			array_push($quizzes,$q);
		}
		return $quizzes;
	}
	
	function getQuizAttempts($ref, $opts = array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
		$sql = sprintf("SELECT ((qascore*100)/ maxscore) as score, firstname, lastname, submitdate FROM quizattempt qa
						LEFT OUTER JOIN user u ON qa.submituser = u.username
						WHERE quizref = '%s'
						AND submitdate > DATE_ADD(NOW(), INTERVAL -%d DAY) 
						ORDER BY submitdate DESC",$ref,$days);

		$summary = array();
		$result = _mysql_query($sql,$this->DB);
		while($o = mysql_fetch_object($result)){
			array_push($summary,$o);
		}
		return $summary;
	}
	
	function quizHasAttempts($ref){
		$sql = sprintf("SELECT * FROM quizattempt WHERE quizref='%s'",$ref);
		$result = _mysql_query($sql,$this->DB);
		while($o = mysql_fetch_object($result)){
			return true;
		}
		return false;
	}
	
	
	function getQuizAttemptsSummary($ref, $opts = array()){
		if(array_key_exists('days',$opts)){
			$days = max(0,$opts['days']);
		} else {
			$days = DEFAULT_DAYS;
		}
		
		$sql = sprintf("SELECT COUNT(*) as no, 
								DAY(submitdate) as day, 
								MONTH(submitdate) as month, 
								YEAR(submitdate) as year 
						FROM quizattempt WHERE quizref='%s' 
						AND submitdate > DATE_ADD(NOW(), INTERVAL -%d DAY) 
						GROUP BY DAY(submitdate), MONTH(submitdate), YEAR(submitdate)",$ref,$days);
		$result = _mysql_query($sql,$this->DB);
		$summary = array();
		if (!$result){
			writeToLog('error','database',$sql);
			return $summary;
		}
		while($o = mysql_fetch_object($result)){
			array_push($summary,$o);
		}
		return $summary;
	}
	
	function getQuizNoAttempts($quizref){
		$sql = sprintf("SELECT Count(*) as noattempts, AVG(qascore*100/maxscore) as avgscore FROM quizattempt
							WHERE quizref = '%s'",$quizref);
		$result = _mysql_query($sql,$this->DB);

		$a = new stdClass;
		$a->noattempts = 0;
		$a->avgscore = 0;
		if (!$result){
			writeToLog('error','database',$sql);
			return $a;
		}
		while($r = mysql_fetch_object($result)){
			$a->noattempts = $r->noattempts;
			if($r->avgscore == null){
				$a->avgscore = 0;
			} else {
				$a->avgscore = $r->avgscore;
			}
				
		}
		return $a;
	}
	
	function getQuizScores($quizref){
		$sql = sprintf("SELECT Count(*) as NoScores, qascore*100/maxscore as scorepercent FROM quizattempt
					WHERE quizref = '%s'
					group by qascore",$quizref);
		$result = _mysql_query($sql,$this->DB);
		$resp = array();
		if (!$result){
			writeToLog('error','database',$sql);
			return $resp;
		}
		while($r = mysql_fetch_object($result)){
			$resp[$r->scorepercent] = $r->NoScores; 
		}
		return $resp;
	}
	
	function getQuizAvgResponseScores($quizref){
		$sql = sprintf("SELECT AVG(qarscore) as avgscore,  questionrefid, langtext FROM quizattemptresponse qar
						INNER JOIN quizattempt qa ON qa.id = qar.qaid
						INNER JOIN language l ON l.langref = qar.questionrefid
						WHERE quizref = '%s'
						GROUP BY questionrefid",$quizref);
		$result = _mysql_query($sql,$this->DB);
		$resp = array();
		if (!$result){
			writeToLog('error','database',$sql);
			return $resp;
		}
		while($r = mysql_fetch_object($result)){
			$resp[$r->langtext] = $r->avgscore;
		}
		return $resp;
	}
	
	function getQuiz($ref){
		$sql = sprintf("SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q
						INNER JOIN language l ON q.quiztitleref = l.langref
						WHERE q.quiztitleref = '%s'",$ref);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($r = mysql_fetch_object($result)){
			$q = new stdClass;
			$q->quizid = $r->quizid;
			$q->ref = $r->quiztitleref;
			$q->title = $r->langtext;
			$q->props = array();
			$psql = sprintf("SELECT * FROM quizprop WHERE quizid = %d",$r->quizid);
			$props = _mysql_query($psql,$this->DB);
			while($prop = mysql_fetch_object($props)){
				$q->props[$prop->quizpropname] = $prop->quizpropvalue;
			}
			return $q;
		}
	}
	
	function getQuizForUser($ref,$userid){
		$sql = sprintf("SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q
						INNER JOIN language l ON q.quiztitleref = l.langref
						WHERE q.quiztitleref = '%s' AND createdby=%d
						ORDER BY l.langtext ASC",$ref,$userid);

		$result = _mysql_query($sql,$this->DB);
		
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		while($r = mysql_fetch_object($result)){
			$q = new stdClass;
			$q->quizid = $r->quizid;
			$q->ref = $r->quiztitleref;
			$q->title = $r->langtext;
			$q->props = array();
			$psql = sprintf("SELECT * FROM quizprop WHERE quizid = %d",$r->quizid);
			$props = _mysql_query($psql,$this->DB);
			while($prop = mysql_fetch_object($props)){
				$q->props[$prop->quizpropname] = $prop->quizpropvalue;
			}
			return $q;
		}
	}
	
	function getQuizQuestions($quizid){
		$sql = sprintf("SELECT q.questionid, q.questiontitleref, qq.orderno, l.langtext FROM question q 
						INNER JOIN quizquestion qq ON qq.questionid = q.questionid
						INNER JOIN language l ON l.langref = q.questiontitleref
						WHERE qq.quizid = %d
						ORDER BY orderno ASC",$quizid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
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
	
	function getQuestionResponses($questionid){
		$sql = sprintf("SELECT r.responseid, r.responsetitleref, qr.orderno, l.langtext, r.score FROM response r 
						INNER JOIN questionresponse qr ON qr.responseid = r.responseid
						INNER JOIN language l ON l.langref = r.responsetitleref
						WHERE qr.questionid = %d
						ORDER BY orderno ASC",$questionid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
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
	
	function addQuiz($title){
		global $USER, $CONFIG;
		$quiztitleref = "qt".$USER->username.uniqid();
		$this->addLang($quiztitleref, $title,$CONFIG->defaultlang);
		
		$str = "INSERT INTO quiz (quiztitleref,createdby) VALUES ('%s',%d)";
		$sql = sprintf($str,$quiztitleref,$USER->userid);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		return $result;
	}
	
	function addQuestion($title){
		global $USER, $CONFIG;
		$questiontitleref = "qqt".$USER->username.uniqid();
		$this->addLang($questiontitleref, $title,$CONFIG->defaultlang);
	
		$str = "INSERT INTO question (questiontitleref,createdby) VALUES ('%s',%d)";
		$sql = sprintf($str,$questiontitleref,$USER->userid);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		return $result;
	}
	
	function addResponse($title,$score){
		global $USER,$CONFIG;
		$responsetitleref = "qqrt".$USER->username.uniqid();
		$this->addLang($responsetitleref, $title,$CONFIG->defaultlang);
	
		$str = "INSERT INTO response (responsetitleref,createdby,score) VALUES ('%s',%d,%d)";
		$sql = sprintf($str,$responsetitleref,$USER->userid,$score);
		mysql_query($sql,$this->DB);
		$result = mysql_insert_id();
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		return $result;
	}
	
	function addQuestionToQuiz($quizid,$questionid,$orderno){
		$str = "INSERT INTO quizquestion (quizid,questionid,orderno) VALUES (%d,%d,%d)";
		$sql = sprintf($str,$quizid,$questionid,$orderno);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		return $result;
	}
	
	function addResponseToQuestion($questionid,$responseid,$orderno){
		$str = "INSERT INTO questionresponse (questionid,responseid,orderno) VALUES (%d,%d,%d)";
		$sql = sprintf($str,$questionid,$responseid,$orderno);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		return $result;
	}
	
	function addLang($ref,$text,$langcode){
		$str = "INSERT INTO language (langref,langtext,langcode) VALUES ('%s','%s','%s')";
		$sql = sprintf($str,$ref,$text,$langcode);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
	}
	
	function setProp($obj,$id,$name,$value){
		// first check to see if it exists already
		$sql = sprintf("SELECT * FROM %sprop WHERE %sid= %d AND %spropname='%s'",$obj,$obj,$id,$obj,$name);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$updateSql = sprintf("UPDATE %sprop SET %spropvalue='%s' WHERE %sid= %d AND %spropname='%s'",$obj,$obj,$value,$obj,$id,$obj,$name);
			_mysql_query($updateSql,$this->DB);
			return;
		}
		
		$insertSql = sprintf("INSERT INTO %sprop (%spropvalue, %sid,%spropname) VALUES ('%s',%d,'%s')",$obj,$obj,$obj,$obj,$value,$id,$name);
		$result = _mysql_query($insertSql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
	}
	
	
	function removeQuiz($ref){
		$questions = $this->getQuizQuestions($ref);
		foreach ($questions as $q){
			$responses = $this->getQuestionResponses($q->id);
			foreach ($responses as $r){
				$this->removeResponse($r->refid);
			}
			$this->removeQuestion($q->refid);
		}
	}
	
	function removeLang($ref){
		$sql = sprintf("DELETE FROM language WHERE langref='%s'",$ref);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
	}
	
	function removeResponse($ref){
		$sql = sprintf("DELETE FROM response WHERE responsetitleref='%s'",$ref);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
	}
	
	function removeQuestion($ref){
		$sql = sprintf("DELETE FROM question WHERE questiontitleref='%s'",$ref);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
	}
	
	function updateLang($ref,$text){
		$sql = sprintf("UPDATE language SET langtext='%s' WHERE langref='%s'",$text,$ref);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
	}
	
	function get10PopularQuizzes(){
		$sql = "SELECT Count(qa.id) as noattempts, qa.quizref, l.langtext FROM quizattempt qa
					INNER JOIN language l ON l.langref = qa.quizref
					GROUP BY qa.quizref
					ORDER BY Count(qa.id) DESC
					LIMIT 0,10";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		$top10 = array();
		while($o = mysql_fetch_object($result)){
			$r = new stdClass();
			$r->ref = $o->quizref;
			$r->title = $o->langtext;
			$r->noattempts = $o->noattempts;
			array_push($top10,$r);
		}
		return $top10;
	}
	
	function get10MostRecentQuizzes(){
		$sql = "SELECT q.quiztitleref,createdon, l.langtext FROM quiz q
					INNER JOIN language l ON l.langref = q.quiztitleref
					ORDER BY createdon DESC
					LIMIT 0,10";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		$top10 = array();
		while($o = mysql_fetch_object($result)){
			$r = new stdClass();
			$r->ref = $o->quiztitleref;
			$r->title = $o->langtext;
			$r->createdon = $o->createdon;
			array_push($top10,$r);
		}
		return $top10;
	}
}