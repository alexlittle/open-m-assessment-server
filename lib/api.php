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
		$username = strtolower($username);
		$email = strtolower($email);
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
		$username = strtolower($username);
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
		$qa->submituser = strtolower($qa->submituser);
		$qa->user = strtolower($qa->submituser);
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
		$sql = "SELECT q.quizid, l.langtext as title, q.quiztitleref as ref FROM quiz q 
				INNER JOIN language l ON q.quiztitleref = l.langref";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$quizzes = array();
		while($r = mysql_fetch_object($result)){
			array_push($quizzes,$r);
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
						INNER JOIN user u ON qa.submituser = u.username
						INNER JOIN quiz q ON q.quiztitleref = qa.quizref
						WHERE quizref = '%s'
						AND submitdate > DATE_ADD(NOW(), INTERVAL -%d DAY) 
						AND u.userid != q.createdby
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
						FROM quizattempt qa
						INNER JOIN user u ON qa.submituser = u.username
						INNER JOIN quiz q ON q.quiztitleref = qa.quizref
						WHERE quizref='%s' 
						AND submitdate > DATE_ADD(NOW(), INTERVAL -%d DAY) 
						AND u.userid != q.createdby
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
		$sql = sprintf("SELECT Count(*) as noattempts, AVG(qascore*100/maxscore) as avgscore FROM quizattempt qa
						INNER JOIN user u ON qa.submituser = u.username
						INNER JOIN quiz q ON q.quiztitleref = qa.quizref
						WHERE quizref = '%s'
						AND u.userid != q.createdby",$quizref);
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
		$sql = sprintf("SELECT Count(*) as NoScores, qascore*100/maxscore as scorepercent FROM quizattempt qa
						INNER JOIN user u ON qa.submituser = u.username
						INNER JOIN quiz q ON q.quiztitleref = qa.quizref
						WHERE quizref = '%s'
						AND u.userid != q.createdby
						GROUP BY qascore",$quizref);
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
		$sql = sprintf("SELECT AVG(qarscore) as avgscore, langtext FROM quizattemptresponse qar
						INNER JOIN quizattempt qa ON qa.id = qar.qaid
						INNER JOIN user u ON qa.submituser = u.username
						INNER JOIN quiz q ON q.quiztitleref = qa.quizref
						INNER JOIN language l ON l.langref = qar.questionrefid
						WHERE quizref = '%s'
						AND u.userid != q.createdby
						GROUP BY langtext",$quizref);
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
	
	function getMyQuizScores(){
		global $USER;
		$sql = sprintf("SELECT AVG(score) as avgscore, count(*) as noattempts, max(score) as maxscore, min(score) as minscore, langtext as title, langref as ref  FROM 
						(SELECT ((qascore*100)/ maxscore) as score,  firstname, lastname, submitdate, l.langtext, l.langref FROM quizattempt qa
						INNER JOIN user u ON qa.submituser = u.username
						INNER JOIN language l ON l.langref = qa.quizref
						WHERE u.userid = %d
						ORDER BY submitdate DESC) a
						GROUP BY langtext, langref", $USER->userid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$results = array();
		while($o = mysql_fetch_object($result)){
			array_push($results,$o);
		}
		return $results;
	}
	
	function getRanking($ref,$userid){
		$sql = sprintf("SELECT * FROM
						(SELECT MAX((qascore*100)/ maxscore) as score,  u.userid, l.langref FROM quizattempt qa
						LEFT OUTER JOIN user u ON qa.submituser = u.username
						INNER JOIN language l ON l.langref = qa.quizref
						WHERE qa.quizref = '%s'
						GROUP BY u.userid, l.langref) a
						ORDER BY score DESC",$ref);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return;
		}
		$rank = 0;
		$count = 0;
		$prevscore = -1;
		$myrank = 0;
		while($o = mysql_fetch_object($result)){
			$count++;
			if($o->score != $prevscore){
				$rank = $count;
				$prevscore = $o->score;
			}
			if($o->userid == $userid){
				$myrank = $rank;
			}
		}
		$r = array("myrank"=>$myrank,"total"=>$count);
		return $r;
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
			$q->props = $this->getQuizProps($r->quizid);
			return $q;
		}
	}
	
	function getQuizById($quizid){
		$sql = sprintf("SELECT q.quizid, l.langtext, q.quiztitleref FROM quiz q
							INNER JOIN language l ON q.quiztitleref = l.langref
							WHERE q.quizid = %d",$quizid);
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
			$q->props = $this->getQuizProps($r->quizid);
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
			$q->props = $this->getQuizProps($r->quizid);
			return $q;
		}
	}
	
	function getQuizProps($quizid){
		$psql = sprintf("SELECT * FROM quizprop WHERE quizid = %d",$quizid);
		$props = _mysql_query($psql,$this->DB);
		$p = array();
		while($prop = mysql_fetch_object($props)){
			$p[$prop->quizpropname] = $prop->quizpropvalue;
		}
		return $p;
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
		$quiztitleref = $this->createUUID("qt");
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
		$questiontitleref = $this->createUUID("qqt");
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
		$responsetitleref = $this->createUUID("qqrt");
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
					INNER JOIN quiz q ON q.quiztitleref = qa.quizref
					INNER JOIN user u ON u.username = qa.submituser
					WHERE u.userid != q.createdby
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
	
	function getLeaderboard(){
		$sql = "SELECT AVG(qascore*100/maxscore) as avgscore, u.firstname, u.lastname FROM quizattempt qa
					INNER JOIN quiz q ON q.quiztitleref = qa.quizref
					INNER JOIN user u ON u.username = qa.submituser
					WHERE u.userid != q.createdby
					GROUP BY u.firstname, u.lastname
					HAVING COUNT(DISTINCT quizref) > 2
					ORDER BY AVG(qascore*100/maxscore) DESC
					LIMIT 0,10";
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return ;
		}
		$leaders = array();
		while($o = mysql_fetch_object($result)){
			array_push($leaders,$o);
		}
		return $leaders;
	}
	
	function addQuizToDownloadQueue($userid,$quizid){
		// find out if already in download queue
		$sql = sprintf("SELECT dlid FROM download
								WHERE userid = %d 
								AND quizid = %d 
								AND queued = true",$userid, $quizid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		if (mysql_num_rows($result) == 0){
			$isql = sprintf("INSERT INTO download (userid,quizid,queued) VALUES (%d,%d,true)",$userid,$quizid);
			$iresult = _mysql_query($isql,$this->DB);
			if (!$result){
				writeToLog('error','database',$isql);
				return false;
			}
		}
		return true;
	}
	
	function getQuizDownloadQueue($userid){
		$sql = sprintf("SELECT DISTINCT q.quiztitleref as quizref FROM download d
						INNER JOIN quiz q ON q.quizid = d.quizid
						WHERE userid = %d 
						AND queued = true",$userid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		$queue = array();
		while($o = mysql_fetch_object($result)){
			array_push($queue, array('quizref'=> $o->quizref));
		}
		return $queue;
	}
	
	function setQuizDownloaded($userid,$quizid){
		// find out if already in queue (so only need to update)
		$sql = sprintf("SELECT dlid FROM download 
						WHERE userid = %d 
						AND quizid = %d 
						AND queued = true",$userid, $quizid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		
		// if queued then just update, otherwise add a record
		if (mysql_num_rows($result) > 0){
			while($o = mysql_fetch_object($result)){
				$usql = sprintf("UPDATE download 
									SET queued = false,
									dldate = now()
								WHERE dlid = %d",$o->dlid);
				$uresult = _mysql_query($usql,$this->DB);
				if (!$uresult){
					writeToLog('error','database',$usql);
					return false;
				}
			}
		} else {
			$isql = sprintf("INSERT INTO download (userid,quizid,queued) VALUES (%d,%d,false)",$userid,$quizid);
			$iresult = _mysql_query($isql,$this->DB);
			if (!$iresult){
				writeToLog('error','database',$isql);
				return false;
			}
		}
		return true;
	}
	
	function getUserDownloadQueue($userid){
		$sql = sprintf("SELECT DISTINCT q.quiztitleref as quizref, l.langtext as quiztitle, dldate as queuedate FROM download d
								INNER JOIN quiz q ON q.quizid = d.quizid
								INNER JOIN language l ON q.quiztitleref = l.langref
								WHERE userid = %d 
								AND queued = true",$userid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		$queue = array();
		while($o = mysql_fetch_object($result)){
			array_push($queue,$o);
		}
		return $queue;
	}
	
	function getUserDownloadHistory($userid){
		$sql = sprintf("SELECT q.quiztitleref as quizref, l.langtext as quiztitle, dldate as historydate FROM download d
						INNER JOIN quiz q ON q.quizid = d.quizid
						INNER JOIN language l ON q.quiztitleref = l.langref
						WHERE userid = %d 
						AND queued = false
						ORDER BY dldate DESC",$userid);
		$result = _mysql_query($sql,$this->DB);
		if (!$result){
			writeToLog('error','database',$sql);
			return false;
		}
		$history = array();
		while($o = mysql_fetch_object($result)){
			array_push($history,$o);
		}
		return $history;
	}
	
	
	function createUUID($prefix){
		global $USER;
		return $prefix.strtolower($USER->firstname).uniqid();
	}
}