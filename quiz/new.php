<?php
include_once("../config.php");
$PAGE = "newquiz";
include_once("../includes/header.php");

$submit = optional_param("submit","",PARAM_TEXT);
$quizprops = array('downloadable','submitable');

$title = optional_param("title","",PARAM_TEXT);
$props = optional_param('props',"",PARAM_TEXT);
$noquestions = optional_param("noquestions",2,PARAM_INT);

if ($submit != ""){
	
	
	$savequiz = true;
	// checks on content, title, quizzes and responses
	// check title
	if($title == ""){
		array_push($MSG, getstring("quiz.new.error.notitle"));
		$savequiz = false;
	}
	// check at least 1 question
	// and at least one response for each question
	$noquest = 0;
	for ($q=1;$q<$noquestions+1;$q++){
		$ref = "q".($q);
		$questiontitle = optional_param($ref,"",PARAM_TEXT);
		if($questiontitle != ""){
			$noquest++;
		}
		$noresponses = 0;
		for ($r=1;$r<5;$r++){
			$rref = "q".($q)."r".($r);
			$responsetitle = optional_param($rref,"",PARAM_TEXT);
			if($responsetitle != ""){
				$noresponses++;
			}
		}
		 
		if ($questiontitle != "" && $noresponses == 0){
			array_push($MSG, "You must enter at least one response for Q".$q);
			$savequiz = false;
		}
		if ($questiontitle == "" && $noresponses > 0){
			array_push($MSG, "You must enter a question for Q".$q);
			$savequiz = false;
		}
	}
	
	if($noquest == 0){
		array_push($MSG, "You must enter at least one question");
		$savequiz = false;
	}
	
	
	if ($savequiz){
		// create the quiz object
		$quizid = $API->addQuiz($title);
		
		$API->setProp('quiz',$quizid,'generatedby','mquiz');
		
		//set the properties
		foreach($quizprops as $qp){
			if(in_array($qp,$props)){
				$API->setProp('quiz',$quizid,$qp,'true');
			} else {
				$API->setProp('quiz',$quizid,$qp,'false');
			}
		}

		$quizmaxscore = 0;
		// create each question
		for ($q=1;$q<$noquestions+1;$q++){
			$ref = "q".($q);
			$questiontitle = optional_param($ref,"",PARAM_TEXT);
			if($questiontitle != ""){
				$questionid = $API->addQuestion($questiontitle);
				$API->addQuestionToQuiz($quizid,$questionid,$q);
				$questionmaxscore = 0;
				// create each response
				for ($r=1;$r<5;$r++){
					$rref = "q".($q)."r".($r);
					$mref = "q".($q)."m".($r);
					$responsetitle = optional_param($rref,"",PARAM_TEXT);
					$score= optional_param($mref,0,PARAM_INT);
					if($responsetitle != ""){
						$responseid = $API->addResponse($responsetitle,$score);
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
		
		$q = $API->getQuizById($quizid);
		
		printf("<div class='info'>%s</div>", getstring("quiz.new.saved"));
		// send mail to owner
		$m = new Mailer();
		$m->sendQuizCreated($USER->email,$USER->firstname, $title, $q->ref);
		include_once("../includes/footer.php");
		die;
	} 
}

?>
<h1><?php echo getstring("quiz.new.title"); ?></h1>
<?php 
if(!empty($MSG)){
	echo "<div class='warning'><ul>";
	foreach ($MSG as $err){
		echo "<li>".$err."</li>";
    }
    echo "</ul></div>";
}
?>

<div id="quizform">

<form method="post" action="">
		<div class="formblock">
			<div class="formlabel"><?php echo getstring('quiz.new.quiztitle'); ?></div>
			<div class="formfield">
				<input type="text" name="title" value="<?php echo $title; ?>" size="60"/><br/>
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
			<h2><?php echo getstring("quiz.new.questions"); ?></h2>
		</div>
		<div id="questions">
			<?php 
				for($q=1; $q<$noquestions+1;$q++){
					$ref = "q".($q);
					$questiontitle = optional_param($ref,"",PARAM_TEXT);
			?>
				<div class="formblock">
					<div class="formlabel"><?php echo getstring('quiz.new.question'); echo " "; echo $q; ?></div>
					<div class="formfield">
						<input type="text" name="q<?php echo $q; ?>" value="<?php echo $questiontitle; ?>" size="60"></input>
						<div class="responses">
							<div class="responsetext">Possible responses</div>
							<div class="responsescore">Score</div>
							<?php 
								for($r=1; $r<5;$r++){ 
									$rref = "q".($q)."r".($r);
									$mref = "q".($q)."m".($r);
									$responsetitle = optional_param($rref,"",PARAM_TEXT);
									$score= optional_param($mref,0,PARAM_INT);
							?>
								<div class="responsetext"><input type="text" name="<?php printf('q%dr%d',$q,$r); ?>" value="<?php echo $responsetitle; ?>" size="40"></input></div>
								<div class="responsescore"><input type="text" name="<?php printf('q%dm%d',$q,$r); ?>" value="<?php echo $score; ?>" size="5"></input></div>
							<?php 
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
			<div class="formfield"><input type="button" name="addquestion" value="<?php echo getstring("quiz.new.add"); ?>" onclick="addQuestion()"/></div>
		</div>
		<div class="formblock">
			<div class="formlabel">&nbsp;</div>
			<div class="formfield"><input type="submit" name="submit" value="<?php echo getstring("quiz.new.submit.button"); ?>"></input></div>
		</div>
		<input type="hidden" id="noquestions" name="noquestions" value="2"/>

</form>
</div>
<?php 
	include_once('optionmenu.php');
?>
<script type="text/javascript">
	//toggleOptionHide();
</script>
<?php 
include_once("../includes/footer.php");
?>