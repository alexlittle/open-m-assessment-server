<?php
include_once("../config.php");
$PAGE = "newquiz";
include_once("../includes/header.php");

$submit = optional_param("submit","",PARAM_TEXT);

if ($submit != ""){
	$title = optional_param("title","",PARAM_TEXT);
	// create the quiz object
	$quizid = $API->addQuiz($title, $_SESSION["session_lang"]);
	
	$noquestions = optional_param("noquestions",0,PARAM_INT);
	$quizmaxscore = 0;
	// create each question
	for ($q=1;$q<$noquestions+1;$q++){
		$ref = "q".($q);
		$questiontitle = optional_param($ref,"",PARAM_TEXT);
		if($questiontitle != ""){
			$questionid = $API->addQuestion($questiontitle, $_SESSION["session_lang"]);
			$API->addQuestionToQuiz($quizid,$questionid,$q);
			$questionmaxscore = 0;
			// create each response
			for ($r=1;$r<5;$r++){
				$rref = "q".($q)."r".($r);
				$mref = "q".($q)."m".($r);
				$responsetitle = optional_param($rref,"",PARAM_TEXT);
				$score= optional_param($mref,0,PARAM_INT);
				if($responsetitle != ""){
					$responseid = $API->addResponse($responsetitle, $_SESSION["session_lang"],$score);
					$API->addResponsetoQuestion($questionid,$responseid,$r);
					$questionmaxscore += $score;
				}
			}
			
			//set max score for question
			$API->setProp('question', $questionid, 'maxscore', $questionmaxscore);
			
			$quizmaxscore += $questionmaxscore;
		}
	}

	// set the maxscore for quiz
	$API->setProp('quiz', $quizid, 'maxscore', $quizmaxscore);
	
	echo "Your quiz has been created!";
	include_once("../includes/footer.php");
	die;
}

?>
<h1><?php echo getstring("quiz.new.title"); ?></h1>
<form method="post" action="">
	<div class="formblock">
		<div class="formlabel"><?php echo getstring('quiz.new.quiztitle'); ?></div>
		<div class="formfield"><input type="text" name="title" value="" size="60"></input></div>
	</div>
	<div class="formblock">
		<h2><?php echo getstring("quiz.new.questions"); ?></h2>
	</div>
	<div id="questions">
		<?php 
			for($q=1; $q<3;$q++){
		?>
			<div class="formblock">
				<div class="formlabel"><?php echo getstring('quiz.new.question'); echo " "; echo $q; ?></div>
				<div class="formfield">
					<input type="text" name="q<?php echo $q; ?>" value="" size="60"></input>
					<h3><?php echo getstring("quiz.new.responses"); ?></h3>
					<?php 
						for($r=1; $r<5;$r++){ 
					?>
						<input type="text" name="<?php printf('q%dr%d',$q,$r); ?>" value="" size="40"></input>
						<input type="text" name="<?php printf('q%dm%d',$q,$r); ?>" value="0" size="5"></input><br/>
					<?php 
						}
					?>

				</div>
			</div>
		<?php 
			}
		?>
		

	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield"><input type="button" name="addquestion" value="<?php echo getstring("quiz.new.add"); ?>" onclick="addQuestion()"/></div>
	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield"><input type="submit" name="submit" value="<?php echo getstring("quiz.new.submit.button"); ?>"></input></div>
	</div>
	<input type="hidden" id="noquestions" name="noquestions" value="2">
</form>

<?php 
include_once("../includes/footer.php");
?>