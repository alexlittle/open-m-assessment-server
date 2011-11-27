<?php 

class Mailer{
	function sendSignUpNotification($name){
		global $CONFIG;
		$email = $CONFIG->emailfrom ;
		$subject = 'mQuiz: New Signup' ;
		$message = $name. ' just signed up to mQuiz' ;
		$headers = 'From: '.$CONFIG->emailfrom;
		mail($email, $subject, $message, $headers );
	}
}

?>