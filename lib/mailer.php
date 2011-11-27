<?php 

class Mailer{
	function sendSignUpNotification($name){
		global $CONFIG;
		$email = $CONFIG->emailfrom ;
		$subject = 'mQuiz: New Signup' ;
		$message = $name. ' just signed up to mQuiz' ;
		$headers = 'From: '.$CONFIG->emailfrom. '\r\n' .
		   'Reply-To: '.$CONFIG->emailfrom.'\r\n' .
		   'X-Mailer: PHP/' . phpversion();
		mail($email, $subject, $message, $headers );
	}
}

?>