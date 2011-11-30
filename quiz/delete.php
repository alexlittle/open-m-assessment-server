<?php
include_once("../config.php");
$PAGE = "editquiz";
include_once("../includes/header.php");
?>
<h1><?php echo getstring("quiz.delete.title"); ?></h1>
<?php
$ref = optional_param('ref',"",PARAM_TEXT);
$q = $API->getQuizForUser($ref,$USER->userid);

if($q == null){
	echo "Quiz not found";
	include_once("../includes/footer.php");
	die;
} 

$delete = optional_param("delete","", PARAM_TEXT);
$cancel = optional_param("cancel","", PARAM_TEXT);
if($delete != ""){
	$API->deleteQuiz($ref);
	echo "<div class='info'>";
	echo "'".$q->title."' has now been deleted.";
	echo "</div>";
	die;
}

if($cancel != ""){
	//return to myquizzes page
	header('Location: '.$CONFIG->homeAddress.'my/quizzes.php');  
    return; 
}

if ($API->quizHasAttempts($ref)){
	printf("<div class='warning'>%s</div>", getstring("warning.quiz.hasattempts"));
}
?>

<div id="quizform">
<form method="post" action="">
	<div class="info">
		You you sure you want to delete the quiz '<?php echo $q->title?>'?
		<p>This will completely remove the quiz and all its questions and cannot be undone.</p>
	</div>
	<div class="formblock">
		<div class="formlabel">&nbsp;</div>
		<div class="formfield">
			<input type="submit" name="delete" value="<?php echo getstring("quiz.delete.button"); ?>"></input>
			<input type="submit" name="cancel" value="<?php echo getstring("quiz.delete.cancel.button"); ?>"></input>
		</div>
	</div>
</form>
</div>

<?php 
include_once("../includes/footer.php");
?>