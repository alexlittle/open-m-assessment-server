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