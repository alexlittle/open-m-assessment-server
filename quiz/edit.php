<?php
include_once("../config.php");
$PAGE = "editquiz";
include_once("../includes/header.php");
?>
<h1><?php echo getstring("quiz.edit.title"); ?></h1>
<?php
$ref = optional_param('ref',"",PARAM_TEXT);
$q = $API->getQuizForUser($ref,$USER->userid);

if($q == null){
	echo "Quiz not found";
	include_once("../includes/footer.php");
	die;
}
$submit = optional_param("submit","",PARAM_TEXT);
if ($submit != ""){
	$title = optional_param("title","",PARAM_TEXT);
	$props = optional_param('props',"",PARAM_TEXT);
	
	if ($title != ""){
		
		//update quiz title
		$API->updateLang($ref,$title);
		
		//update quiz props
		$quizprops = array('downloadable','submitable');
		foreach($quizprops as $qp){
			if(is_array($props) && in_array($qp,$props)){
				$API->setProp('quiz',$q->quizid,$qp,'true');
			} else {
				$API->setProp('quiz',$q->quizid,$qp,'false');
			}
		}
		
		
		// remove quiz questions and responses
		// easier for now to just remove and recreate :-(
		$API->removeQuiz($q->quizid);
		
		// create the quiz object
		$quizid = $q->quizid;
	
		$noquestions = optional_param("noquestions",0,PARAM_INT);
		$quizmaxscore = 0;
		// create each question
		for ($i=1;$i<$noquestions+1;$i++){
			$qref = "q".($i);
			$questiontitle = optional_param($qref,"",PARAM_TEXT);
			if($questiontitle != ""){
				$questionid = $API->addQuestion($questiontitle);
				$API->addQuestionToQuiz($quizid,$questionid,$i);
				$questionmaxscore = 0;
				// create each response
				for ($j=1;$j<5;$j++){
					$rref = "q".($i)."r".($j);
					$mref = "q".($i)."m".($j);
					$responsetitle = optional_param($rref,"",PARAM_TEXT);
					$score= optional_param($mref,0,PARAM_INT);
					if($responsetitle != ""){
						$responseid = $API->addResponse($responsetitle,$score);
						$API->addResponsetoQuestion($questionid,$responseid,$j);
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
	
		printf("<div class='info'>%s</div>", getstring("quiz.edit.saved"));
	}
	//reload quiz (to get updated title)
	$q = $API->getQuizForUser($ref,$USER->userid);
}


$qq = $API->getQuizQuestions($q->quizid);

if ($API->quizHasAttempts($ref)){
	printf("<div class='warning'>%s</div>", getstring("warning.quiz.hasattempts"));
}

?>

<form method="post" action="">
	<div class="formblock">
		<div class="formlabel"><?php echo getstring('quiz.edit.quiztitle'); ?></div>
		<div class="formfield">
			<input type="text" name="title" value="<?php echo $q->title; ?>" size="60"/><br/>
			<!-- <span id="optionsshow" onclick="toggleOptionShow();" class="link">Show quiz options</span>
				<span id="optionshide" onclick="toggleOptionHide();" class="link">Hide quiz options</span> -->
		</div>
	</div>
	<div id="options" class="formblock">
			<div class='formlabel'>&nbsp;</div>
			<div class='formfield'>
				<?php include_once('options.php')?>
			</div>
		</div>
	<div class="formblock">
		<h2><?php echo getstring("quiz.edit.questions"); ?></h2>
	</div>
	<div id="questions">
		<?php 
			for($i=1; $i<count($qq)+1;$i++){
		?>
			<div class="formblock">
				<div class="formlabel"><?php echo getstring('quiz.edit.question'); echo " "; echo $i; ?></div>
				<div class="formfield">
					<input type="text" name="q<?php echo $i; ?>" value="<?php echo $qq[$i-1]->text; ?>" size="60"></input>
					<div class="responses">
					<div class="responsetext">Possible responses</div><div class="responsescore">Score</div>
					<?php 
						$qqr = $API->getQuestionResponses($qq[$i-1]->id);
						for($j=1; $j<5;$j++){ 
							if (isset($qqr[$j-1])){
					?>
						<div class="responsetext"><input type="text" name="<?php printf('q%dr%d',$i,$j); ?>" value="<?php echo $qqr[$j-1]->text; ?>" size="40"></input></div>
						<div class="responsescore"><input type="text" name="<?php printf('q%dm%d',$i,$j); ?>" value="<?php echo $qqr[$j-1]->score; ?>" size="5"></input></div>
					<?php 
							} else {
					?>
						<div class="responsetext"><input type="text" name="<?php printf('q%dr%d',$i,$j); ?>" value="" size="40"></input></div>
						<div class="responsescore"><input type="text" name="<?php printf('q%dm%d',$i,$j); ?>" value="0" size="5"></input></div>
					<?php
							}
						}
					?>
					</div>
				</div>
			</div>
		<?php 
			}
		?>
		

	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield"><input type="button" name="addquestion" value="<?php echo getstring("quiz.edit.add"); ?>" onclick="addQuestion()"/></div>
	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield"><input type="submit" name="submit" value="<?php echo getstring("quiz.edit.submit.button"); ?>"></input></div>
	</div>
	<input type="hidden" id="noquestions" name="noquestions" value="<?php echo count($qq); ?>">
</form>
<script type="text/javascript">
//toggleOptionHide();
</script>
<?php 
include_once("../includes/footer.php");
?>