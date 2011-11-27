<?php 

class Mailer{
	
	
	function sendQuizCreated($to,$name, $quiztitle, $quizrefid){
		global $CONFIG;
		$subject = 'mQuiz: Quiz created' ;
		$url_edit = $CONFIG->homeAddress."quiz/edit.php?ref=".$quizrefid;
		$url_download = $CONFIG->homeAddress."my/download.php?ref=".$quizrefid;
		$message = "
			<p>Hi ".$name.",</p>
			<p>Your new mQuiz '".$quiztitle."' has been created.</p>
			<p>To edit your quiz visit: <a href='".$url_edit."'>".$url_edit."</a>.</p>
			<p>To share your quiz with others please use: <a href='".$url_download."'>".$url_download."</a>.</p>
			<p>We hope you enjoy using mQuiz!</p>
			<p>Alex: alex@mquiz.org</p>
		";
		$this->sendMail($to,$subject,$message);
		$this->sendMail("alex@alexlittle.net",$subject,$message);
	}
	
	function sendSignUpNotification($name){
		global $CONFIG;
		$to = $CONFIG->emailfrom ;
		$subject = 'mQuiz: New Signup' ;
		$message = $name. ' just signed up to mQuiz' ;
		$this->sendMail($to,$subject,$message);
	}
	
	private function sendMail($to,$subject,$message){
		global $CONFIG;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: ".$CONFIG->emailfrom . "\r\n";
		mail($to, $subject, $message, $headers );
	}
}

?>